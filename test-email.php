<?php
/**
 * Script de prueba para envío de correo electrónico
 * Uso: php test-email.php [email@destino.com]
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

// Obtener email destino desde argumentos o usar uno por defecto
$toEmail = $argv[1] ?? 'destinatario@ejemplo.com';

echo "===========================================\n";
echo "PRUEBA DE ENVÍO DE CORREO ELECTRÓNICO\n";
echo "===========================================\n\n";

echo "Configuración actual:\n";
echo "- Mailer: " . config('mail.default') . "\n";
echo "- Host: " . config('mail.mailers.smtp.host') . "\n";
echo "- Port: " . config('mail.mailers.smtp.port') . "\n";
echo "- Username: " . config('mail.mailers.smtp.username') . "\n";
echo "- From: " . config('mail.from.address') . "\n";
echo "- To: " . $toEmail . "\n\n";

try {
    echo "Enviando correo de prueba...\n";
    
    Mail::raw('Este es un correo de prueba desde el sistema de Arrendamientos Badillo. Si recibes este mensaje, la configuración de correo está funcionando correctamente.', function ($message) use ($toEmail) {
        $message->to($toEmail)
                ->subject('🧪 Prueba de Correo - Sistema Arrendamientos Badillo')
                ->from(config('mail.from.address'), config('mail.from.name'));
    });
    
    echo "\n✅ ¡Correo enviado exitosamente!\n";
    echo "Revisa la bandeja de entrada (y spam) de: $toEmail\n\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error al enviar correo:\n";
    echo $e->getMessage() . "\n\n";
    echo "Detalles completos del error:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

echo "===========================================\n";
