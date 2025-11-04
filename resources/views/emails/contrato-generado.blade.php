<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato Generado - InmoLegal</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #555;
            margin-bottom: 25px;
        }
        .token-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .token-box strong {
            color: #667eea;
            font-size: 14px;
            text-transform: uppercase;
            display: block;
            margin-bottom: 8px;
        }
        .token-box .token {
            font-family: 'Courier New', monospace;
            font-size: 24px;
            font-weight: bold;
            color: #333;
            letter-spacing: 2px;
        }
        .info-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #667eea;
        }
        .info-item {
            margin: 8px 0;
            font-size: 14px;
        }
        .info-item strong {
            color: #555;
            min-width: 120px;
            display: inline-block;
        }
        .attachments {
            background: #ecfdf5;
            border-left: 4px solid #10b981;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .attachments h3 {
            margin: 0 0 10px 0;
            color: #065f46;
            font-size: 16px;
        }
        .attachments ul {
            margin: 0;
            padding-left: 20px;
        }
        .attachments li {
            margin: 5px 0;
            color: #047857;
        }
        .important {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .important strong {
            color: #92400e;
            font-size: 14px;
        }
        .important p {
            margin: 8px 0 0 0;
            font-size: 14px;
            color: #78350f;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
            font-size: 14px;
            color: #6b7280;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .header h1 {
                font-size: 24px;
            }
            .token-box .token {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 Contrato Generado Exitosamente</h1>
            <p>InmoLegal - Sistema de Contratos de Arrendamiento</p>
        </div>

        <div class="content">
            <div class="greeting">
                Estimado/a {{ $contrato->nombres_arrendatario }} {{ $contrato->apellido_paterno_arrendatario }},
            </div>

            <div class="message">
                Su contrato de arrendamiento ha sido generado exitosamente y el pago inicial ha sido procesado correctamente. 
                Adjunto a este correo encontrará dos documentos importantes:
            </div>

            <div class="attachments">
                <h3>📎 Documentos Adjuntos</h3>
                <ul>
                    <li><strong>Recibo de Pago</strong> - Comprobante de su pago inicial</li>
                    <li><strong>Contrato de Arrendamiento</strong> - Documento legal completo</li>
                </ul>
            </div>

            <div class="token-box">
                <strong>Token de Seguimiento</strong>
                <div class="token">{{ $contrato->token }}</div>
            </div>

            <div class="info-box">
                <h3>📋 Resumen del Contrato</h3>
                <div class="info-item">
                    <strong>Inmueble:</strong> {{ $contrato->calle }} #{{ $contrato->numero_exterior }}, {{ $contrato->colonia }}
                </div>
                <div class="info-item">
                    <strong>Ciudad:</strong> {{ $contrato->ciudad }}, {{ $contrato->codigo_estado }}
                </div>
                <div class="info-item">
                    <strong>Renta Mensual:</strong> ${{ number_format($contrato->precio_mensual, 2) }} MXN
                </div>
                <div class="info-item">
                    <strong>Plazo:</strong> {{ $contrato->plazo_meses }} meses
                </div>
                <div class="info-item">
                    <strong>Fecha de Inicio:</strong> {{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}
                </div>
                <div class="info-item">
                    <strong>Día de Pago:</strong> {{ $contrato->dia_pago }}
                </div>
                <div class="info-item">
                    <strong>Forma de Pago:</strong> {{ ucfirst($contrato->forma_pago) }}
                </div>
            </div>

            <div class="important">
                <strong>⚠️ Importante</strong>
                <p>
                    • Conserve este correo y los documentos adjuntos para su referencia.<br>
                    • Su token de seguimiento es único y le permite consultar su contrato en cualquier momento.<br>
                    • Para cualquier aclaración, presente este token junto con su identificación oficial.<br>
                    • Los documentos adjuntos tienen validez legal.
                </p>
            </div>

            <div class="message">
                Si tiene alguna pregunta o necesita asistencia, no dude en contactarnos.
            </div>

            <div class="message" style="margin-top: 30px;">
                Atentamente,<br>
                <strong>Equipo de InmoLegal</strong>
            </div>
        </div>

        <div class="footer">
            <p><strong>InmoLegal</strong></p>
            <p>Sistema de Generación de Contratos de Arrendamiento</p>
            <p>
                <a href="https://www.inmolegalmx.com">www.inmolegalmx.com</a> | 
                <a href="mailto:soporte@inmolegalmx.com">soporte@inmolegalmx.com</a>
            </p>
            <p style="font-size: 12px; color: #9ca3af; margin-top: 15px;">
                Este es un correo automático, por favor no responda directamente a este mensaje.
            </p>
        </div>
    </div>
</body>
</html>
