<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✅ Pago Exitoso - InmoLegal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 60px 40px;
            text-align: center;
        }
        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease-out;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        h1 {
            color: #059669;
            font-size: 32px;
            margin-bottom: 15px;
        }
        p {
            color: #64748b;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .token {
            background: #f1f5f9;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: 2px;
            margin: 30px 0;
            border: 2px dashed #cbd5e1;
        }
        .info-box {
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
        }
        .info-box h3 {
            color: #166534;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .info-box ul {
            list-style: none;
            padding-left: 0;
        }
        .info-box li {
            color: #166534;
            padding: 8px 0;
            border-bottom: 1px solid #d1fae5;
        }
        .info-box li:last-child {
            border-bottom: none;
        }
        .btn-download {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 20px;
        }
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        .email-sent {
            color: #475569;
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <h1>¡Pago Exitoso!</h1>
        <p>Su contrato de arrendamiento ha sido generado correctamente</p>

        <div class="token">
            <div style="font-size: 14px; color: #64748b; margin-bottom: 5px;">Token de descarga:</div>
            {{ $contrato->token }}
        </div>

        <div class="info-box">
            <h3>📧 Información importante</h3>
            <ul>
                <li>✓ Contrato generado exitosamente</li>
                <li>✓ Email enviado a: <strong>{{ $contrato->email }}</strong></li>
                <li>✓ Token válido por 30 días</li>
                <li>✓ Descargas ilimitadas</li>
            </ul>
        </div>

        @if($contrato->pagado)
            <a href="{{ $downloadUrl }}" class="btn-download">
                📥 Descargar Contrato PDF
            </a>
        @else
            <div style="margin-top: 20px; padding: 15px; background: #fef3c7; border-radius: 10px;">
                <p style="color: #92400e;">⏳ Pago pendiente. Recibirá un email cuando se confirme el pago.</p>
            </div>
        @endif

        <p class="email-sent">
            💌 Hemos enviado el link de descarga a su correo electrónico.<br>
            Revise también su carpeta de spam.
        </p>

        <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e2e8f0;">
            <p style="font-size: 14px; color: #94a3b8;">
                Si tiene alguna duda, conserve su token:<br>
                <strong>{{ $contrato->token }}</strong>
            </p>
        </div>
    </div>
</body>
</html>
