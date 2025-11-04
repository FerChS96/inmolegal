<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle Contrato - InmoLegal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f3f4f6;
            color: #1f2937;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar h1 {
            font-size: 24px;
            font-weight: 700;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .navbar a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }
        .section {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .section h2 {
            font-size: 20px;
            color: #667eea;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 16px;
            color: #1f2937;
        }
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        .actions {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .token-display {
            font-family: 'Courier New', monospace;
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            padding: 16px;
            background: #f3f4f6;
            border-radius: 8px;
            text-align: center;
            letter-spacing: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        th {
            background: #f9fafb;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>📄 Detalle del Contrato</h1>
        <a href="{{ route('admin.contratos') }}">← Volver a Contratos</a>
    </nav>

    <div class="container">
        <div class="section">
            <h2>Token de Seguimiento</h2>
            <div class="token-display">{{ $contrato->token }}</div>
            <div class="actions">
                <a href="{{ route('pdf.recibo', $contrato->token) }}" class="btn btn-primary" target="_blank">
                    📄 Descargar Recibo
                </a>
                <a href="{{ route('pdf.contrato', $contrato->token) }}" class="btn btn-primary" target="_blank">
                    📋 Descargar Contrato
                </a>
            </div>
        </div>

        <div class="section">
            <h2>Estado del Contrato</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Estado de Pago</div>
                    <div class="info-value">
                        @if($contrato->fecha_pago)
                            <span class="badge badge-success">✓ Pagado</span>
                        @else
                            <span class="badge badge-warning">⏳ Pendiente</span>
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha de Pago</div>
                    <div class="info-value">
                        {{ $contrato->fecha_pago ? \Carbon\Carbon::parse($contrato->fecha_pago)->format('d/m/Y H:i') : 'N/A' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Monto Pagado</div>
                    <div class="info-value">
                        ${{ number_format($contrato->monto_pagado ?? 0, 2) }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Método de Pago</div>
                    <div class="info-value">{{ $contrato->metodo_pago ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Información del Arrendatario</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nombre Completo</div>
                    <div class="info-value">
                        {{ $contrato->nombres_arrendatario }} 
                        {{ $contrato->apellido_paterno_arrendatario }} 
                        {{ $contrato->apellido_materno_arrendatario }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">CURP</div>
                    <div class="info-value">{{ $contrato->curp_arrendatario }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $contrato->email }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Teléfono</div>
                    <div class="info-value">{{ $contrato->telefono ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Información del Arrendador</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nombre Completo</div>
                    <div class="info-value">
                        {{ $contrato->nombres_arrendador }} 
                        {{ $contrato->apellido_paterno_arrendador }} 
                        {{ $contrato->apellido_materno_arrendador }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">CURP</div>
                    <div class="info-value">{{ $contrato->curp_arrendador }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Inmueble</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Dirección Completa</div>
                    <div class="info-value">
                        {{ $contrato->calle }} #{{ $contrato->numero_exterior }}
                        @if($contrato->numero_interior), Int. {{ $contrato->numero_interior }}@endif,
                        Col. {{ $contrato->colonia }},
                        {{ $contrato->ciudad }}, {{ $contrato->codigo_estado }}
                        C.P. {{ $contrato->codigo_postal }}
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Detalles del Contrato</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Fecha de Inicio</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Plazo</div>
                    <div class="info-value">{{ $contrato->plazo_meses }} meses</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Renta Mensual</div>
                    <div class="info-value">${{ number_format($contrato->precio_mensual, 2) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Forma de Pago</div>
                    <div class="info-value">{{ ucfirst($contrato->forma_pago) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Día de Pago</div>
                    <div class="info-value">{{ $contrato->dia_pago }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha de Creación</div>
                    <div class="info-value">{{ $contrato->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

        @if($pagos->count() > 0)
        <div class="section">
            <h2>Historial de Pagos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Pago</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Fecha de Pago</th>
                        <th>Fecha de Creación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pagos as $pago)
                    <tr>
                        <td style="font-family: 'Courier New', monospace; font-size: 12px;">
                            {{ $pago->payment_request_id }}
                        </td>
                        <td>${{ number_format($pago->amount, 2) }}</td>
                        <td>
                            @if($pago->status === 'paid')
                                <span class="badge badge-success">✓ Pagado</span>
                            @else
                                <span class="badge badge-warning">⏳ Pendiente</span>
                            @endif
                        </td>
                        <td>
                            {{ $pago->paid_at ? \Carbon\Carbon::parse($pago->paid_at)->format('d/m/Y H:i') : 'N/A' }}
                        </td>
                        <td>{{ $pago->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</body>
</html>
