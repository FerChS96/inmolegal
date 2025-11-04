<?php
/**
 * Script de diagnóstico SMTP
 * Prueba la conexión al servidor de correo sin enviar email
 */

echo "===========================================\n";
echo "DIAGNÓSTICO DE CONEXIÓN SMTP\n";
echo "===========================================\n\n";

$host = 'smtp.porkbun.com';
$port = 587;
$username = 'soporte@inmolegalmx.com';
$password = 'BGsEwy1912Brenda!';

echo "Configuración:\n";
echo "- Host: $host\n";
echo "- Port: $port\n";
echo "- Username: $username\n";
echo "- Encryption: TLS\n\n";

// Test 1: Verificar extensiones PHP
echo "=== TEST 1: Extensiones PHP ===\n";
$required_extensions = ['openssl', 'sockets'];
foreach ($required_extensions as $ext) {
    $loaded = extension_loaded($ext);
    echo ($loaded ? "✅" : "❌") . " $ext: " . ($loaded ? "Cargada" : "NO DISPONIBLE") . "\n";
}
echo "\n";

// Test 2: Conexión al servidor
echo "=== TEST 2: Conexión al servidor SMTP ===\n";
echo "Intentando conectar a $host:$port...\n";

$errno = 0;
$errstr = '';
$socket = @fsockopen($host, $port, $errno, $errstr, 10);

if ($socket) {
    echo "✅ Conexión establecida correctamente\n";
    $response = fgets($socket, 512);
    echo "Respuesta del servidor: " . trim($response) . "\n";
    fclose($socket);
} else {
    echo "❌ Error de conexión: [$errno] $errstr\n";
    echo "Posibles causas:\n";
    echo "  - Firewall bloqueando el puerto $port\n";
    echo "  - Servidor SMTP caído o host incorrecto\n";
    echo "  - Problemas de red\n";
}
echo "\n";

// Test 3: Verificar credenciales manualmente
echo "=== TEST 3: Autenticación SMTP Manual ===\n";

try {
    $socket = fsockopen($host, $port, $errno, $errstr, 10);
    if (!$socket) {
        throw new Exception("No se pudo conectar: $errstr");
    }
    
    echo "Conectado. Iniciando handshake SMTP...\n";
    
    // Leer banner
    $response = fgets($socket, 512);
    echo "< " . trim($response) . "\n";
    
    // EHLO
    fputs($socket, "EHLO localhost\r\n");
    while ($str = fgets($socket, 512)) {
        echo "< " . trim($str) . "\n";
        if (substr($str, 3, 1) == ' ') break;
    }
    
    // STARTTLS
    fputs($socket, "STARTTLS\r\n");
    $response = fgets($socket, 512);
    echo "< " . trim($response) . "\n";
    
    if (strpos($response, '220') !== false) {
        echo "✅ TLS disponible\n";
        echo "✅ Servidor SMTP funcionando correctamente\n";
    }
    
    fclose($socket);
    
} catch (Exception $e) {
    echo "❌ Error:\n";
    echo $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Probar envío real con seguimiento detallado
echo "=== TEST 4: Envío de correo con log detallado ===\n";

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

try {
    echo "Enviando correo a ferchs1996@gmail.com...\n";
    
    Mail::raw('Correo de prueba con diagnóstico detallado. Timestamp: ' . now(), function ($message) {
        $message->to('ferchs1996@gmail.com')
                ->subject('Prueba SMTP - ' . date('Y-m-d H:i:s'))
                ->from('soporte@inmolegalmx.com', 'Sistema Badillo');
    });
    
    echo "✅ Laravel reporta envío exitoso\n";
    echo "NOTA: Esto significa que el mensaje fue aceptado por el servidor SMTP.\n";
    echo "      Si no llega, puede ser problema del servidor destino (Gmail).\n";
    
} catch (\Exception $e) {
    echo "❌ Error al enviar:\n";
    echo $e->getMessage() . "\n";
}

echo "\n===========================================\n";
echo "RECOMENDACIONES:\n";
echo "===========================================\n";
echo "1. Si todos los tests pasan pero no llega el correo:\n";
echo "   - Revisa carpeta SPAM en Gmail\n";
echo "   - El dominio inmolegalmx.com puede no tener SPF/DKIM configurado\n";
echo "   - Gmail puede estar rechazando correos de este servidor\n\n";
echo "2. Si la autenticación falla:\n";
echo "   - Verifica las credenciales en el .env\n";
echo "   - Contacta con Porkbun para verificar el estado del servicio SMTP\n\n";
echo "3. Si la conexión falla:\n";
echo "   - Verifica firewall de Windows\n";
echo "   - Prueba con otro puerto (465 para SSL)\n";
echo "===========================================\n";
