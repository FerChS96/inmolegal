<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos - InmoLegal</title>
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
        .nav-links {
            display: flex;
            gap: 16px;
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
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px;
        }
        .filters {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .filters form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: end;
        }
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #374151;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }
        .btn-secondary:hover {
            background: #d1d5db;
        }
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }
        th {
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
        }
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }
        tr:hover {
            background: #f9fafb;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
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
        .badge-error {
            background: #fee2e2;
            color: #991b1b;
        }
        .token-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }
        .token-link:hover {
            text-decoration: underline;
        }
        .pagination {
            padding: 20px;
            display: flex;
            justify-content: center;
            gap: 8px;
        }
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            color: #374151;
            background: #f3f4f6;
        }
        .pagination a:hover {
            background: #e5e7eb;
        }
        .pagination .active {
            background: #667eea;
            color: white;
        }
        .no-results {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
        .payment-id {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>💳 Pagos</h1>
        <div class="nav-links">
            <a href="{{ route('admin.panel') }}">← Panel</a>
            <a href="{{ route('admin.contratos') }}">Contratos</a>
            <a href="{{ route('admin.logout') }}">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container">
        <div class="filters">
            <form method="GET" action="{{ route('admin.pagos') }}">
                <div class="form-group">
                    <label>Buscar</label>
                    <input type="text" name="search" placeholder="ID de pago, token, email..." value="{{ request('search') }}">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="status">
                        <option value="">Todos</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pagado</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Fallido</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('admin.pagos') }}" class="btn btn-secondary">Limpiar</a>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Pago</th>
                        <th>Token Contrato</th>
                        <th>Arrendatario</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Fecha Pago</th>
                        <th>Fecha Creación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagos as $pago)
                    <tr>
                        <td>
                            <div class="payment-id">{{ $pago->payment_request_id }}</div>
                        </td>
                        <td>
                            @if($pago->contrato)
                                <a href="{{ route('admin.ver-contrato', $pago->contrato->token) }}" class="token-link">
                                    {{ $pago->contrato->token }}
                                </a>
                            @else
                                <span style="color: #9ca3af;">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($pago->contrato)
                                {{ $pago->contrato->nombres_arrendatario }} 
                                {{ $pago->contrato->apellido_paterno_arrendatario }}
                            @else
                                <span style="color: #9ca3af;">N/A</span>
                            @endif
                        </td>
                        <td>${{ number_format($pago->amount, 2) }}</td>
                        <td>
                            @if($pago->status === 'paid')
                                <span class="badge badge-success">✓ Pagado</span>
                            @elseif($pago->status === 'pending')
                                <span class="badge badge-warning">⏳ Pendiente</span>
                            @else
                                <span class="badge badge-error">✗ Fallido</span>
                            @endif
                        </td>
                        <td>
                            @if($pago->paid_at)
                                {{ \Carbon\Carbon::parse($pago->paid_at)->format('d/m/Y H:i') }}
                            @else
                                <span style="color: #9ca3af;">-</span>
                            @endif
                        </td>
                        <td>{{ $pago->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="no-results">
                            No se encontraron pagos
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($pagos->hasPages())
            <div class="pagination">
                {{ $pagos->links() }}
            </div>
            @endif
        </div>
    </div>
</body>
</html>
