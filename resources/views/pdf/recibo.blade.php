<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago - InmoLegal</title>
    <style>
        @page {
            margin: 15mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #1f2933;
        }
        .logo-section {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
        }
        .logo-section img {
            max-width: 180px;
            height: auto;
        }
        .header {
            background: #10b981;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
        }
        .token-section {
            background: #eff6ff;
            border: 2px dashed #185abc;
            border-radius: 5px;
            padding: 12px;
            text-align: center;
            margin-bottom: 15px;
        }
        .token-label {
            font-size: 9px;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .token-value {
            font-size: 18px;
            font-weight: bold;
            color: #185abc;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }
        .amount-section {
            background: #ecfdf5;
            border: 2px solid #10b981;
            border-radius: 5px;
            padding: 12px;
            text-align: center;
            margin-bottom: 15px;
        }
        .amount-label {
            font-size: 10px;
            color: #047857;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .amount-value {
            font-size: 24px;
            font-weight: bold;
            color: #065f46;
        }
        .info-section {
            margin-bottom: 12px;
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
        }
        .info-section h2 {
            font-size: 11px;
            color: #185abc;
            margin-bottom: 8px;
            text-transform: uppercase;
            border-bottom: 1px solid #185abc;
            padding-bottom: 4px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 35%;
            padding: 3px 5px;
            color: #6b7280;
            font-size: 9pt;
        }
        .info-value {
            display: table-cell;
            padding: 3px 5px;
            color: #1f2933;
            font-size: 9pt;
        }
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 7pt;
            color: #6b7280;
        }
        .important-box {
            background: #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 8px;
            margin: 10px 0;
            border-radius: 3px;
            font-size: 8pt;
        }
        .important-box strong {
            color: #92400e;
            font-size: 9pt;
        }
        .two-columns {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }
        .column {
            display: table-cell;
            width: 50%;
            padding-right: 5px;
            vertical-align: top;
        }
        .column:last-child {
            padding-right: 0;
            padding-left: 5px;
        }
    </style>
</head>
<body>
    <div class="logo-section">
        <img src="{{ public_path('images/logo-inmolegal.svg') }}" alt="InmoLegal">
    </div>

    <div class="header">
        <h1>✓ RECIBO DE PAGO</h1>
        <p>Contrato de Arrendamiento - InmoLegal</p>
    </div>

    <div class="token-section">
        <div class="token-label">Token de Seguimiento</div>
        <div class="token-value">{{ $contrato->token }}</div>
    </div>

    <div class="amount-section">
        <div class="amount-label">MONTO PAGADO</div>
        <div class="amount-value">${{ number_format($contrato->precio_mensual, 2) }} MXN</div>
    </div>

    <div class="two-columns">
        <div class="column">
            <div class="info-section">
                <h2>Arrendatario</h2>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Nombre:</div>
                        <div class="info-value">{{ strtoupper($contrato->nombres_arrendatario . ' ' . $contrato->apellido_paterno_arrendatario . ' ' . $contrato->apellido_materno_arrendatario) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">CURP:</div>
                        <div class="info-value">{{ strtoupper($contrato->curp_arrendatario) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value">{{ $contrato->email }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="column">
            <div class="info-section">
                <h2>Contrato</h2>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Fecha Inicio:</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Plazo:</div>
                        <div class="info-value">{{ $contrato->plazo_meses }} meses</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Renta:</div>
                        <div class="info-value">${{ number_format($contrato->precio_mensual, 2) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Forma Pago:</div>
                        <div class="info-value">{{ strtoupper($contrato->forma_pago) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h2>Inmueble</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Dirección:</div>
                <div class="info-value">
                    {{ strtoupper($contrato->calle) }} #{{ $contrato->numero_exterior }}@if($contrato->numero_interior), INT. {{ $contrato->numero_interior }}@endif, COL. {{ strtoupper($contrato->colonia) }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Ciudad/Estado:</div>
                <div class="info-value">{{ strtoupper($contrato->ciudad) }}, {{ strtoupper($contrato->codigo_estado) }} - CP {{ $contrato->codigo_postal }}</div>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h2>Pago</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Método:</div>
                <div class="info-value">{{ strtoupper($contrato->metodo_pago ?? 'CLIP - TARJETA') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($contrato->fecha_pago)->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Estado:</div>
                <div class="info-value"><strong>✓ PAGADO</strong></div>
            </div>
        </div>
    </div>

    <div class="important-box">
        <strong>⚠ IMPORTANTE:</strong> Este recibo es válido como comprobante de pago inicial. Conserve su token para futuras consultas.
    </div>

    <div class="footer">
        <p><strong>InmoLegal</strong> - Sistema de Generación de Contratos</p>
        <p>www.inmolegalmx.com | Documento generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
