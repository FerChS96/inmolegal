<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - InmoLegal</title>
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }
        .stat-card h3 {
            color: #6b7280;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .stat-card .value {
            font-size: 36px;
            font-weight: 700;
            color: #667eea;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .menu-card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }
        .menu-card .icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        .menu-card h2 {
            font-size: 24px;
            margin-bottom: 8px;
            color: #1f2937;
        }
        .menu-card p {
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>🏢 InmoLegal - Panel de Administración</h1>
        <a href="{{ route('admin.logout') }}">Cerrar Sesión</a>
    </nav>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Contratos</h3>
                <div class="value">{{ $totalContratos }}</div>
            </div>
            <div class="stat-card">
                <h3>Contratos Pagados</h3>
                <div class="value">{{ $contratosPagados }}</div>
            </div>
            <div class="stat-card">
                <h3>Pagos Procesados</h3>
                <div class="value">{{ $totalPagos }}</div>
            </div>
            <div class="stat-card">
                <h3>Monto Total</h3>
                <div class="value">${{ number_format($montoTotal, 2) }}</div>
            </div>
        </div>

        <div class="menu-grid">
            <a href="{{ route('admin.contratos') }}" class="menu-card">
                <div class="icon">📄</div>
                <h2>Contratos</h2>
                <p>Ver y gestionar todos los contratos generados</p>
            </a>

            <a href="{{ route('admin.pagos') }}" class="menu-card">
                <div class="icon">💳</div>
                <h2>Pagos</h2>
                <p>Consultar historial de pagos y transacciones</p>
            </a>
        </div>
    </div>
</body>
</html>
