<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Contrato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ClipWebhookController extends Controller
{
    /**
     * Manejar webhook de Clip
     * Clip enviará notificaciones POST a este endpoint cuando ocurran eventos de pago
     */
    public function handleWebhook(Request $request)
    {
        // Registrar toda la información del webhook para debugging
        Log::info('Clip Webhook recibido', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'raw' => $request->getContent(),
            'ip' => $request->ip(),
        ]);

        try {
            // Obtener datos del webhook
            $payload = $request->all();
            
            // Validar que tenga los datos mínimos necesarios
            if (!isset($payload['type']) || !isset($payload['data'])) {
                Log::warning('Webhook de Clip con formato inválido', ['payload' => $payload]);
                return response()->json(['error' => 'Invalid webhook format'], 400);
            }

            $eventType = $payload['type'];
            $data = $payload['data'];

            Log::info("Procesando webhook de tipo: {$eventType}", ['data' => $data]);

            // Procesar según el tipo de evento
            switch ($eventType) {
                case 'payment.paid':
                case 'charge.paid':
                case 'checkout.paid':
                    return $this->handlePaymentPaid($data, $payload);

                case 'payment.failed':
                case 'charge.failed':
                case 'checkout.failed':
                    return $this->handlePaymentFailed($data, $payload);

                case 'payment.refunded':
                case 'charge.refunded':
                    return $this->handlePaymentRefunded($data, $payload);

                default:
                    Log::info("Evento de webhook no manejado: {$eventType}");
                    return response()->json(['message' => 'Event received but not processed'], 200);
            }

        } catch (\Exception $e) {
            Log::error('Error procesando webhook de Clip', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Manejar pago exitoso
     */
    private function handlePaymentPaid($data, $fullPayload)
    {
        try {
            // Buscar el pago por payment_request_id o clip_payment_id
            $pago = null;
            
            if (isset($data['id'])) {
                $pago = Pago::where('clip_payment_id', $data['id'])
                           ->orWhere('payment_request_id', $data['id'])
                           ->first();
            }

            // Si no se encuentra por ID, buscar por monto y estado pendiente
            if (!$pago && isset($data['amount'])) {
                $pago = Pago::where('amount', $data['amount'])
                           ->where('status', 'pending')
                           ->orderBy('created_at', 'desc')
                           ->first();
            }

            if (!$pago) {
                Log::warning('No se encontró el pago asociado al webhook', ['data' => $data]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Actualizar el pago
            $pago->update([
                'status' => 'paid',
                'paid_at' => now(),
                'clip_payment_id' => $data['id'] ?? $pago->clip_payment_id,
                'payment_method' => $data['payment_method'] ?? null,
                'clip_payment_response' => $data,
                'webhook_received' => true,
                'webhook_data' => $fullPayload,
                'webhook_received_at' => now(),
                'webhook_attempts' => ($pago->webhook_attempts ?? 0) + 1,
                'processed' => true,
            ]);

            Log::info('Pago actualizado por webhook', [
                'pago_id' => $pago->idpago,
                'payment_id' => $data['id'] ?? null
            ]);

            // Buscar el contrato asociado
            $contrato = Contrato::find($pago->idcontrato);

            if (!$contrato) {
                Log::error('No se encontró el contrato asociado al pago', [
                    'pago_id' => $pago->idpago,
                    'contrato_id' => $pago->idcontrato
                ]);
                return response()->json(['error' => 'Contract not found'], 404);
            }

            // Actualizar el contrato
            $contrato->update([
                'fecha_pago' => now(),
                'monto_pagado' => $pago->amount,
                'metodo_pago' => 'Clip - ' . ($data['payment_method'] ?? 'Tarjeta'),
            ]);

            Log::info('Contrato actualizado por webhook', [
                'contrato_id' => $contrato->idcontrato,
                'token' => $contrato->token
            ]);

            // Enviar email con PDFs (si no se envió antes)
            if (!$pago->notification_sent) {
                $this->enviarEmailConPDFs($contrato, $pago);
            }

            return response()->json([
                'message' => 'Payment processed successfully',
                'payment_id' => $pago->idpago,
                'contract_token' => $contrato->token
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error procesando pago exitoso', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return response()->json(['error' => 'Error processing payment'], 500);
        }
    }

    /**
     * Manejar pago fallido
     */
    private function handlePaymentFailed($data, $fullPayload)
    {
        try {
            $pago = Pago::where('clip_payment_id', $data['id'] ?? null)
                       ->orWhere('payment_request_id', $data['id'] ?? null)
                       ->first();

            if (!$pago) {
                Log::warning('No se encontró el pago para marcar como fallido', ['data' => $data]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            $pago->update([
                'status' => 'failed',
                'error_code' => $data['error_code'] ?? null,
                'error_message' => $data['error_message'] ?? 'Payment failed',
                'webhook_received' => true,
                'webhook_data' => $fullPayload,
                'webhook_received_at' => now(),
                'webhook_attempts' => ($pago->webhook_attempts ?? 0) + 1,
            ]);

            Log::info('Pago marcado como fallido por webhook', [
                'pago_id' => $pago->idpago,
                'error' => $data['error_message'] ?? 'Unknown'
            ]);

            return response()->json(['message' => 'Payment failure recorded'], 200);

        } catch (\Exception $e) {
            Log::error('Error procesando pago fallido', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return response()->json(['error' => 'Error processing failure'], 500);
        }
    }

    /**
     * Manejar reembolso
     */
    private function handlePaymentRefunded($data, $fullPayload)
    {
        try {
            $pago = Pago::where('clip_payment_id', $data['id'] ?? null)
                       ->first();

            if (!$pago) {
                Log::warning('No se encontró el pago para marcar como reembolsado', ['data' => $data]);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            $pago->update([
                'refunded' => true,
                'refund_id' => $data['refund_id'] ?? null,
                'refund_amount' => $data['refund_amount'] ?? $pago->amount,
                'refund_reason' => $data['refund_reason'] ?? 'Refund processed',
                'refunded_at' => now(),
                'webhook_received' => true,
                'webhook_data' => $fullPayload,
                'webhook_received_at' => now(),
            ]);

            Log::info('Pago marcado como reembolsado por webhook', [
                'pago_id' => $pago->idpago,
                'refund_amount' => $data['refund_amount'] ?? $pago->amount
            ]);

            return response()->json(['message' => 'Refund recorded'], 200);

        } catch (\Exception $e) {
            Log::error('Error procesando reembolso', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return response()->json(['error' => 'Error processing refund'], 500);
        }
    }

    /**
     * Enviar email con PDFs adjuntos
     */
    private function enviarEmailConPDFs($contrato, $pago)
    {
        try {
            // Generar PDFs en memoria
            $pdfRecibo = app('dompdf.wrapper')
                ->loadView('pdf.recibo', ['contrato' => $contrato])
                ->output();

            $pdfContrato = app('dompdf.wrapper')
                ->loadView('pdf.contrato', ['contrato' => $contrato])
                ->output();

            // Enviar email con PDFs adjuntos
            \Mail::to($contrato->email)->send(new \App\Mail\ContratoGenerado($contrato, $pdfRecibo, $pdfContrato));

            // Marcar como enviado
            $pago->update(['notification_sent' => true]);

            Log::info('Email con PDFs enviado exitosamente', [
                'email' => $contrato->email,
                'token' => $contrato->token
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando email con PDFs', [
                'error' => $e->getMessage(),
                'contrato_token' => $contrato->token
            ]);
        }
    }

    /**
     * Endpoint para verificar que el webhook está funcionando
     */
    public function test(Request $request)
    {
        Log::info('Test webhook recibido', [
            'method' => $request->method(),
            'data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Webhook endpoint is working',
            'timestamp' => now()->toISOString(),
            'received_data' => $request->all()
        ], 200);
    }
}
