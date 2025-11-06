<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Pendiente - InmoLegal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }
        .token {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 5px;
            letter-spacing: 2px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            opacity: 0.9;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏠 InmoLegal</h1>
            <p>Pago en Efectivo Pendiente</p>
        </div>

        <div class="content">
            <h2>¡Hola!</h2>
            
            <p>Hemos recibido tu solicitud de contrato de arrendamiento. Para completar el proceso, necesitamos que realices el pago de <strong>${{ number_format($pago->amount, 2) }} MXN</strong> en efectivo.</p>

            <div class="info-box">
                <h3>📋 Información de tu Contrato</h3>
                <p><strong>Token de Contrato:</strong></p>
                <div class="token">{{ $contrato->token }}</div>
                <p><strong>Arrendador:</strong> {{ $contrato->nombres_arrendador }} {{ $contrato->apellido_paterno_arrendador }} {{ $contrato->apellido_materno_arrendador }}</p>
                <p><strong>Arrendatario:</strong> {{ $contrato->nombres_arrendatario }} {{ $contrato->apellido_paterno_arrendatario }} {{ $contrato->apellido_materno_arrendatario }}</p>
                <p><strong>Monto a Pagar:</strong> ${{ number_format($pago->amount, 2) }} MXN</p>
            </div>

            <div class="warning">
                <h3>⚠️ Importante</h3>
                <p><strong>Tu contrato aún no ha sido generado.</strong> El PDF del contrato se generará automáticamente cuando completemos la confirmación de tu pago.</p>
                <p>Por favor realiza el pago en efectivo usando el siguiente enlace:</p>
            </div>

            <div class="center">
                <a href="{{ $checkoutUrl }}" class="button">
                    💰 Completar Pago en Efectivo
                </a>
            </div>

            <div class="info-box">
                <h3>📝 ¿Qué sigue?</h3>
                <ol>
                    <li>Haz clic en el botón "Completar Pago en Efectivo"</li>
                    <li>Sigue las instrucciones para generar tu referencia de pago</li>
                    <li>Realiza el pago en el establecimiento indicado (OXXO, 7-Eleven, etc.)</li>
                    <li>Una vez confirmado el pago, recibirás otro correo con:
                        <ul>
                            <li>✅ PDF del Contrato de Arrendamiento</li>
                            <li>✅ PDF del Recibo de Pago</li>
                        </ul>
                    </li>
                </ol>
            </div>

            <p><strong>Guarda tu token:</strong> {{ $contrato->token }}</p>
            <p>Podrás usar este token para consultar el estado de tu pago y descargar tus documentos una vez procesado.</p>

            <p>Si tienes alguna duda, no dudes en contactarnos.</p>

            <p>Saludos,<br>
            <strong>Equipo InmoLegal</strong></p>
        </div>

        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} InmoLegal. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
