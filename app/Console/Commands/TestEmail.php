<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'email:test {email? : Email address to send test to}';
    protected $description = 'Send a test email to verify SMTP configuration';

    public function handle()
    {
        $email = $this->argument('email') ?? 'ferchs1996@gmail.com';
        
        $this->info('====================================');
        $this->info('PRUEBA DE ENVÍO DE CORREO');
        $this->info('====================================');
        $this->newLine();
        
        $this->info("Configuración SMTP:");
        $this->line("- Mailer: " . config('mail.default'));
        $this->line("- Host: " . config('mail.mailers.smtp.host'));
        $this->line("- Port: " . config('mail.mailers.smtp.port'));
        $this->line("- From: " . config('mail.from.address'));
        $this->line("- To: " . $email);
        $this->newLine();
        
        try {
            $this->info('Enviando correo...');
            
            Mail::raw(
                "Este es un correo de prueba del sistema Arrendamientos Badillo.\n\n" .
                "Timestamp: " . now()->format('Y-m-d H:i:s') . "\n" .
                "Servidor: " . config('mail.mailers.smtp.host') . "\n\n" .
                "Si recibes este mensaje, la configuración SMTP está funcionando correctamente.",
                function ($message) use ($email) {
                    $message->to($email)
                            ->subject('✅ Test SMTP - Sistema Badillo - ' . now()->format('H:i:s'))
                            ->from(config('mail.from.address'), config('mail.from.name'));
                }
            );
            
            $this->newLine();
            $this->info('✅ Correo enviado exitosamente!');
            $this->line("Revisa la bandeja de entrada de: {$email}");
            $this->line("También revisa la carpeta de SPAM.");
            $this->newLine();
            
            // Información adicional
            $this->comment('Nota: Si el correo no llega, puede deberse a:');
            $this->line('  • Configuración SPF/DKIM del dominio');
            $this->line('  • Filtros anti-spam de Gmail');
            $this->line('  • Reputación del servidor remitente');
            
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
}
