<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso - InmoLegal</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
          integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Calibri', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            padding: 30px;
            color: #1f2933;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 700px;
            width: 100%;
            background: #ffffff;
            border: 1px solid #d0d7e3;
            border-radius: 14px;
            box-shadow: 0 12px 32px rgba(15, 40, 72, 0.12);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(120deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header-icon {
            font-size: 72px;
            background: rgba(255, 255, 255, 0.2);
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.5s ease-out;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 12px;
        }
        .header p {
            font-size: 18px;
            opacity: 0.95;
        }
        .content {
            padding: 40px;
        }
        .success-message {
            background: #ecfdf5;
            border: 2px solid #10b981;
            border-radius: 10px;
            padding: 24px;
            margin-bottom: 32px;
            text-align: center;
        }
        .success-message h2 {
            color: #065f46;
            font-size: 24px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .success-message p {
            color: #047857;
            font-size: 16px;
            line-height: 1.6;
        }
        .token-section {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 28px;
            margin-bottom: 32px;
        }
        .token-label {
            font-size: 14px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            display: block;
        }
        .token-value {
            font-size: 28px;
            font-weight: 700;
            color: #185abc;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            text-align: center;
            padding: 16px;
            background: white;
            border: 2px dashed #185abc;
            border-radius: 8px;
            word-break: break-all;
        }
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #185abc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .info-box h3 {
            color: #1e40af;
            font-size: 18px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-box ul {
            color: #1e3a8a;
            padding-left: 20px;
            line-height: 1.8;
        }
        .info-box li {
            margin-bottom: 8px;
        }
        .actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(120deg, #185abc 0%, #1f7ae0 100%);
            color: white;
        }
        .btn-primary:hover {
            background: linear-gradient(120deg, #164a9f 0%, #1a68c4 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(24, 90, 188, 0.3);
        }
        .btn-secondary {
            background: white;
            color: #185abc;
            border: 2px solid #185abc;
        }
        .btn-secondary:hover {
            background: #f0f7ff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(24, 90, 188, 0.15);
        }
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 32px 0;
        }
        .footer-text {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }
        @media (max-width: 640px) {
            body {
                padding: 20px;
            }
            .header {
                padding: 32px 24px;
            }
            .content {
                padding: 28px 20px;
            }
            .token-value {
                font-size: 22px;
            }
            .actions {
                flex-direction: column;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>¡Pago Exitoso!</h1>
            <p>Tu transacción ha sido procesada correctamente</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Success Message -->
            <div class="success-message">
                <h2>
                    <i class="fas fa-file-contract"></i>
                    Contrato Generado
                </h2>
                <p>
                    Tu contrato de arrendamiento ha sido generado exitosamente.<br>
                    Guarda tu token de seguimiento para futuras consultas.
                </p>
            </div>

            <!-- Token Section -->
            <div class="token-section">
                <span class="token-label">
                    <i class="fas fa-key"></i> Token de Seguimiento
                </span>
                <div class="token-value">
                    {{ $token }}
                </div>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <h3>
                    <i class="fas fa-info-circle"></i>
                    Próximos Pasos
                </h3>
                <ul>
                    <li>✓ Tus documentos se descargarán automáticamente en un momento</li>
                    <li>✓ Recibirás un correo electrónico con el recibo y contrato en formato PDF</li>
                    <li>✓ Usa tu token para consultar el estatus de tu contrato</li>
                    <li>✓ El contrato está legalmente vinculante una vez firmado por ambas partes</li>
                    <li>✓ Conserva este token para cualquier aclaración o consulta futura</li>
                </ul>
            </div>

            <div class="divider"></div>

            <!-- Actions -->
            <div class="actions">
                <a href="{{ route('pdf.recibo', ['token' => $token]) }}" class="btn btn-primary">
                    <i class="fas fa-receipt"></i>
                    Descargar Recibo
                </a>
                <a href="{{ route('pdf.contrato', ['token' => $token]) }}" class="btn btn-primary">
                    <i class="fas fa-file-contract"></i>
                    Descargar Contrato
                </a>
            </div>

            <div class="divider"></div>

            <div class="actions">
                <a href="{{ route('contrato.formulario') }}" class="btn btn-secondary">
                    <i class="fas fa-plus-circle"></i>
                    Generar Nuevo Contrato
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="fas fa-print"></i>
                    Imprimir Página
                </button>
            </div>

            <div class="divider"></div>

            <!-- Footer Text -->
            <div class="footer-text">
                <p>
                    <strong>InmoLegal</strong> - Sistema de Generación de Contratos<br>
                    Si tienes alguna duda o problema, contacta con soporte.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Copiar token al portapapeles al hacer clic
        document.querySelector('.token-value').addEventListener('click', function() {
            const token = this.textContent.trim();
            navigator.clipboard.writeText(token).then(() => {
                const original = this.textContent;
                this.textContent = '✓ Copiado';
                setTimeout(() => {
                    this.textContent = original;
                }, 2000);
            });
        });

        // Descargar automáticamente los PDFs después de 2 segundos
        setTimeout(function() {
            // Descargar recibo
            const linkRecibo = document.createElement('a');
            linkRecibo.href = '{{ route("pdf.recibo", ["token" => $token]) }}';
            linkRecibo.download = 'recibo_{{ $token }}.pdf';
            document.body.appendChild(linkRecibo);
            linkRecibo.click();
            document.body.removeChild(linkRecibo);

            // Descargar contrato después de 1 segundo
            setTimeout(function() {
                const linkContrato = document.createElement('a');
                linkContrato.href = '{{ route("pdf.contrato", ["token" => $token]) }}';
                linkContrato.download = 'contrato_{{ $token }}.pdf';
                document.body.appendChild(linkContrato);
                linkContrato.click();
                document.body.removeChild(linkContrato);
            }, 1000);
        }, 2000);
    </script>
</body>
</html>
