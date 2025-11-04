<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contrato;
use App\Mail\ContratoGenerado;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class SimularContratoCompleto extends Command
{
    protected $signature = 'contrato:simular {email? : Email donde enviar el contrato}';
    protected $description = 'Simula el proceso completo de creación de contrato y envío de correo con PDFs';

    public function handle()
    {
        $email = $this->argument('email') ?? 'contacto@veqsum.net';
        
        $this->info('═══════════════════════════════════════════════════');
        $this->info('   SIMULACIÓN DE CONTRATO DE ARRENDAMIENTO');
        $this->info('═══════════════════════════════════════════════════');
        $this->newLine();
        
        // Paso 1: Crear contrato de prueba
        $this->info('📝 Paso 1: Creando contrato de prueba...');
        $contrato = $this->crearContratoSimulado($email);
        $this->line("   ✅ Contrato creado con token: {$contrato->token}");
        $this->newLine();
        
        // Paso 2: Generar PDFs
        $this->info('📄 Paso 2: Generando PDFs...');
        $pdfs = $this->generarPDFs($contrato);
        
        if (!$pdfs) {
            $this->error('❌ Error al generar PDFs');
            return 1;
        }
        
        $this->line('   ✅ PDF del recibo generado');
        $this->line('   ✅ PDF del contrato generado');
        $this->newLine();
        
        // Paso 3: Enviar correo
        $this->info('📧 Paso 3: Enviando correo electrónico...');
        $this->mostrarDetallesContrato($contrato);
        $this->newLine();
        
        try {
            Mail::to($email)->send(
                new ContratoGenerado($contrato, $pdfs['recibo'], $pdfs['contrato'])
            );
            
            $this->newLine();
            $this->info('═══════════════════════════════════════════════════');
            $this->info('✅ ¡CORREO ENVIADO EXITOSAMENTE!');
            $this->info('═══════════════════════════════════════════════════');
            $this->newLine();
            
            $this->line("📬 Destinatario: <fg=cyan>{$email}</>");
            $this->line("🎫 Token del contrato: <fg=yellow>{$contrato->token}</>");
            $this->line("📎 Adjuntos:");
            $this->line("   • recibo-{$contrato->token}.pdf");
            $this->line("   • contrato-{$contrato->token}.pdf");
            $this->newLine();
            
            $this->comment('💡 Revisa tu bandeja de entrada (y la carpeta de spam)');
            $this->newLine();
            
            // Mostrar información adicional
            $this->info('ℹ️  Información del contrato de prueba:');
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['ID Contrato', $contrato->idcontrato],
                    ['Token', $contrato->token],
                    ['Email', $contrato->email],
                    ['Arrendador', $contrato->nombres_arrendador . ' ' . $contrato->apellido_paterno_arrendador],
                    ['Arrendatario', $contrato->nombres_arrendatario . ' ' . $contrato->apellido_paterno_arrendatario],
                    ['Tipo Inmueble', $contrato->tipo_inmueble],
                    ['Dirección', $contrato->calle . ' ' . $contrato->numero_exterior . ', ' . $contrato->colonia],
                    ['Ciudad', $contrato->ciudad],
                    ['Precio Mensual', '$' . number_format($contrato->precio_mensual, 2)],
                    ['Plazo', $contrato->plazo_meses . ' meses'],
                    ['Fecha Inicio', $contrato->fecha_inicio->format('d/m/Y')],
                ]
            );
            
            return 0;
            
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Error al enviar correo:');
            $this->error($e->getMessage());
            $this->newLine();
            
            if ($this->output->isVerbose()) {
                $this->line($e->getTraceAsString());
            }
            
            return 1;
        }
    }
    
    private function crearContratoSimulado($email)
    {
        $contrato = Contrato::create([
            'token' => Contrato::generateToken(),
            'email' => $email,
            
            // Arrendador (Propietario)
            'nombres_arrendador' => 'María Guadalupe',
            'apellido_paterno_arrendador' => 'Hernández',
            'apellido_materno_arrendador' => 'López',
            'curp_arrendador' => 'HELM850615MDFRPR03',
            
            // Arrendatario (Inquilino)
            'nombres_arrendatario' => 'Juan Carlos',
            'apellido_paterno_arrendatario' => 'Pérez',
            'apellido_materno_arrendatario' => 'García',
            'curp_arrendatario' => 'PEGJ920420HDFRRC09',
            
            // Fiador (opcional)
            'tiene_fiador' => true,
            'nombres_fiador' => 'Roberto',
            'apellido_paterno_fiador' => 'Martínez',
            'apellido_materno_fiador' => 'Sánchez',
            'curp_fiador' => 'MASR880315HDFRTN08',
            
            // Inmueble
            'tipo_inmueble' => 'Casa',
            'uso_inmueble' => 'Habitacional',
            
            // Dirección
            'calle' => 'Av. Insurgentes Sur',
            'numero_exterior' => '1234',
            'numero_interior' => 'Depto 5B',
            'colonia' => 'Del Valle',
            'codigo_postal' => '03100',
            'ciudad' => 'Ciudad de México',
            'codigo_estado' => 'CDMX',
            
            // Contrato
            'fecha_inicio' => now()->addDays(7),
            'plazo_meses' => 12,
            'precio_mensual' => 15000.00,
            'forma_pago' => 'Transferencia bancaria',
            'cuenta_domicilio' => 'Cuenta bancaria BBVA terminación 1234',
            
            // Estado del pago (simulado como pagado)
            'pagado' => true,
            'monto_pagado' => 1200.00,
            'fecha_pago' => now(),
            'metodo_pago' => 'Tarjeta de crédito',
            'pago_id' => 'TEST_' . strtoupper(uniqid()),
        ]);
        
        return $contrato;
    }
    
    private function generarPDFs(Contrato $contrato)
    {
        try {
            // Generar PDF del recibo como string (output)
            $pdfRecibo = Pdf::loadView('pdf.recibo', ['contrato' => $contrato])->output();
            
            // Generar PDF del contrato como string (output)
            $pdfContrato = Pdf::loadView('pdf.contrato', ['contrato' => $contrato])->output();
            
            return [
                'recibo' => $pdfRecibo,
                'contrato' => $pdfContrato,
            ];
            
        } catch (\Exception $e) {
            $this->error('Error al generar PDFs: ' . $e->getMessage());
            return null;
        }
    }
    
    private function mostrarDetallesContrato(Contrato $contrato)
    {
        $this->line('📋 Detalles del correo a enviar:');
        $this->line('   • Asunto: Contrato de Arrendamiento - InmoLegal #' . $contrato->token);
        $this->line('   • Para: ' . $contrato->email);
        $this->line('   • De: ' . config('mail.from.address'));
        $this->line('   • Adjuntos: 2 PDFs (recibo + contrato)');
    }
}
