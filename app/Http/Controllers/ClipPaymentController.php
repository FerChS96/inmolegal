<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Contrato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ClipPaymentController extends Controller
{
    private $apiKey;
    private $secretKey;
    private $apiUrl;

    public function __construct()
    {
        // Configurar según ambiente (sandbox/producción)
        $this->apiKey = env('CLIP_API_KEY');
        $this->secretKey = env('CLIP_SECRET_KEY');
        $this->apiUrl = env('CLIP_API_URL', 'https://api-gw.payclip.com');
    }
    
    /**
     * Generar token Basic Auth en Base64
     */
    private function getAuthToken()
    {
        return base64_encode($this->apiKey . ':' . $this->secretKey);
    }

    /**
     * Iniciar proceso de pago con Clip
     */
    public function iniciarPago(Pago $pago)
    {
        try {
            // Verificar que el pago esté pendiente
            if ($pago->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pago ya fue procesado'
                ], 400);
            }

            $contrato = $pago->contrato;
            
            if (!$contrato) {
                Log::error('Contrato no encontrado para el pago', [
                    'pago_id' => $pago->idpago,
                    'idcontrato' => $pago->idcontrato
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Contrato no encontrado'
                ], 404);
            }

            // Crear checkout link en Clip
            $authToken = $this->getAuthToken();
            
            Log::info('Intentando crear checkout en Clip', [
                'api_key' => substr($this->apiKey, 0, 15) . '...', 
                'api_url' => $this->apiUrl . '/v2/checkout',
                'auth_token_preview' => 'Basic ' . substr($authToken, 0, 20) . '...',
                'amount' => $pago->amount,
            ]);
            
            $response = Http::withOptions([
                'verify' => false, // Deshabilitar verificación SSL solo para desarrollo
            ])->withHeaders([
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'authorization' => 'Basic ' . $authToken,
            ])->post($this->apiUrl . '/v2/checkout', [
                'amount' => $pago->amount,
                'currency' => 'MXN',
                'purchase_description' => $pago->description,
                'redirection_url' => [
                    'success' => route('clip.success', ['token' => $contrato->token]),
                    'error' => route('clip.error', ['token' => $contrato->token]),
                    'default' => route('clip.cancel', ['token' => $contrato->token]),
                ],
                'webhook_url' => route('webhook.clip'),
                'metadata' => [
                    'contrato_id' => $contrato->idcontrato,
                    'token' => $contrato->token,
                    'customer_email' => $pago->customer_email,
                ],
            ]);

            if ($response->successful()) {
                $clipData = $response->json();
                
                Log::info('Checkout creado exitosamente', [
                    'payment_request_id' => $clipData['payment_request_id'] ?? null,
                    'payment_url' => $clipData['payment_request_url'] ?? null,
                ]);
                
                // Actualizar registro de pago con datos de Clip
                $pago->update([
                    'payment_request_id' => $clipData['payment_request_id'],
                    'checkout_url' => $clipData['payment_request_url'],
                    'payment_created_at' => $clipData['created_at'] ?? now(),
                    'payment_expires_at' => $clipData['expires_at'] ?? null,
                    'clip_checkout_response' => $clipData,
                ]);

                // Redirigir al checkout de Clip
                return redirect($clipData['payment_request_url']);
            } else {
                $clipError = $response->json();
                
                Log::error('Error al crear checkout en Clip', [
                    'response' => $clipError,
                    'status' => $response->status(),
                    'pago_id' => $pago->idpago,
                    'headers_sent' => [
                        'x-api-key' => substr($this->apiKey, 0, 10) . '...',
                        'accept' => 'application/vnd.com.payclip.v2+json',
                    ],
                    'body_sent' => [
                        'amount' => $pago->amount,
                        'currency' => 'MXN',
                    ],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar el pago con Clip',
                    'clip_error' => config('app.debug') ? $clipError : null,
                    'status_code' => $response->status()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Excepción al iniciar pago Clip', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'pago_id' => $pago->idpago ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error inesperado al procesar el pago',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Webhook para recibir notificaciones de Clip
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('Webhook recibido de Clip', $request->all());

            // Validar que venga de Clip (verificar firma si Clip la envía)
            // TODO: Implementar verificación de firma según documentación de Clip

            $clipPaymentId = $request->input('payment_id');
            $status = $request->input('status');

            // Buscar el pago por payment_request_id o clip_payment_id
            $pago = Pago::where('payment_request_id', $request->input('payment_request_id'))
                        ->orWhere('clip_payment_id', $clipPaymentId)
                        ->firstOrFail();

            // Actualizar datos del pago
            $pago->update([
                'clip_payment_id' => $clipPaymentId,
                'status' => $this->mapearEstadoClip($status),
                'webhook_received_at' => now(),
            ]);

            // Si el pago fue exitoso
            if ($status === 'COMPLETED' || $status === 'paid') {
                $this->procesarPagoExitoso($pago, $request->all());
            }

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            Log::error('Error en webhook de Clip', [
                'message' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Procesar pago exitoso
     */
    private function procesarPagoExitoso(Pago $pago, array $clipData)
    {
        try {
            // Actualizar registro de pago con todos los datos de Clip
            // NOTA: NO guardamos información de tarjeta por falta de certificación PCI DSS
            $pago->update([
                'status' => 'paid',
                'paid_at' => now(),
                'authorization_code' => $clipData['authorization_code'] ?? null,
                'transaction_id' => $clipData['transaction_id'] ?? null,
                'clip_response' => $clipData,
            ]);

            // Marcar contrato como pagado
            $contrato = $pago->contrato;
            $contrato->marcarComoPagado($pago->idpago);

            // Generar PDFs en memoria (no se guardan en servidor)
            $pdfs = $this->generarPDFs($contrato);

            // Enviar email con PDFs adjuntos
            if ($pdfs) {
                $this->enviarEmailConPDFs($contrato, $pdfs);
            }

            Log::info('Pago procesado exitosamente', [
                'pago_id' => $pago->idpago,
                'contrato_id' => $contrato->idcontrato,
            ]);

        } catch (\Exception $e) {
            Log::error('Error al procesar pago exitoso', [
                'pago_id' => $pago->idpago,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mapear estado de Clip a nuestro sistema
     */
    private function mapearEstadoClip($clipStatus)
    {
        $mapa = [
            'PENDING' => 'pending',
            'COMPLETED' => 'paid',
            'paid' => 'paid',
            'FAILED' => 'failed',
            'failed' => 'failed',
            'CANCELLED' => 'cancelled',
            'cancelled' => 'cancelled',
            'REFUNDED' => 'refunded',
        ];

        return $mapa[$clipStatus] ?? 'pending';
    }

    /**
     * Página de éxito después del pago
     */
    public function success($token)
    {
        $contrato = Contrato::where('token', $token)->firstOrFail();
        
        return view('clip.success', [
            'token' => $token,
            'contrato' => $contrato,
        ]);
    }

    /**
     * Descargar recibo PDF (generado bajo demanda)
     */
    public function descargarRecibo($token)
    {
        $contrato = Contrato::where('token', $token)->firstOrFail();
        
        $pdf = Pdf::loadView('pdf.recibo', ['contrato' => $contrato]);
        
        return $pdf->download('recibo_' . $token . '.pdf');
    }

    /**
     * Descargar contrato PDF (generado bajo demanda)
     */
    public function descargarContrato($token)
    {
        $contrato = Contrato::where('token', $token)->firstOrFail();
        
        $pdf = Pdf::loadView('pdf.contrato', ['contrato' => $contrato]);
        
        return $pdf->download('contrato_' . $token . '.pdf');
    }

    /**
     * Página de error en el pago
     */
    public function error($token)
    {
        $contrato = Contrato::where('token', $token)->firstOrFail();
        return view('contratos.error', ['contrato' => $contrato]);
    }

    /**
     * Página de cancelación del pago
     */
    public function cancel($token)
    {
        $contrato = Contrato::where('token', $token)->firstOrFail();
        return view('contratos.cancel', ['contrato' => $contrato]);
    }

    /**
     * Consultar estado del pago en Clip
     */
    public function consultarEstado(Pago $pago)
    {
        try {
            $response = Http::withHeaders([
                'accept' => 'application/vnd.com.payclip.v2+json',
                'x-api-key' => $this->apiKey,
            ])->get($this->apiUrl . '/checkout/' . $pago->payment_request_id);

            if ($response->successful()) {
                $data = $response->json();
                
                return response()->json([
                    'success' => true,
                    'status' => $data['status'],
                    'data' => $data,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo consultar el estado del pago',
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error al consultar estado en Clip', [
                'pago_id' => $pago->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al consultar el estado',
            ], 500);
        }
    }

    /**
     * Generar PDFs temporalmente (no se guardan en servidor)
     * Retorna las rutas temporales de los PDFs generados
     */
    private function generarPDFs(Contrato $contrato)
    {
        try {
            // Generar PDF del recibo en memoria
            $pdfRecibo = Pdf::loadView('pdf.recibo', ['contrato' => $contrato]);
            
            // Generar PDF del contrato en memoria
            $pdfContrato = Pdf::loadView('pdf.contrato', ['contrato' => $contrato]);
            
            Log::info('PDFs generados en memoria', [
                'token' => $contrato->token,
            ]);
            
            return [
                'recibo' => $pdfRecibo,
                'contrato' => $pdfContrato,
            ];
            
        } catch (\Exception $e) {
            Log::error('Error al generar PDFs', [
                'token' => $contrato->token,
                'error' => $e->getMessage(),
            ]);
            
            return null;
        }
    }

    /**
     * Enviar email con PDFs adjuntos en memoria
     */
    private function enviarEmailConPDFs(Contrato $contrato, array $pdfs)
    {
        try {
            // Enviar email con PDFs adjuntos
            \Mail::to($contrato->email)->send(
                new \App\Mail\ContratoGenerado($contrato, $pdfs['recibo'], $pdfs['contrato'])
            );
            
            Log::info('Email enviado exitosamente', [
                'token' => $contrato->token,
                'email' => $contrato->email,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al enviar email', [
                'token' => $contrato->token,
                'email' => $contrato->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

