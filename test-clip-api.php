<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== Test de API de Clip ===" . PHP_EOL;
echo "API Key: " . substr(env('CLIP_API_KEY'), 0, 20) . "..." . PHP_EOL;
echo "API URL: " . env('CLIP_API_URL') . PHP_EOL;
echo PHP_EOL;

echo "Enviando request de prueba (con Basic Auth)..." . PHP_EOL;

// Probar con Basic Auth: API_KEY:SECRET_KEY
$response = Http::withOptions([
    'verify' => false,
])->withBasicAuth(env('CLIP_API_KEY'), env('CLIP_SECRET_KEY'))
  ->withHeaders([
    'accept' => 'application/json',
    'content-type' => 'application/json',
])->post(env('CLIP_API_URL') . '/v2/checkout', [
    'amount' => 100.5,
    'currency' => 'MXN',
    'purchase_description' => 'ejemplo de compra',
    'redirection_url' => [
        'success' => 'https://oceanairti.sytes.net/inmolegal/success',
        'error' => 'https://oceanairti.sytes.net/inmolegal/error',
        'default' => 'https://oceanairti.sytes.net/inmolegal',
    ],
]);

echo PHP_EOL;
echo "Status Code: " . $response->status() . PHP_EOL;
echo "Response Body: " . PHP_EOL;
echo $response->body() . PHP_EOL;
echo PHP_EOL;

if ($response->successful()) {
    echo "✅ Conexión exitosa!" . PHP_EOL;
} else {
    echo "❌ Error en la conexión" . PHP_EOL;
}
