<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>❌ Error en el Pago</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
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
        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        h1 {
            color: #dc2626;
            font-size: 32px;
            margin-bottom: 15px;
        }
        p {
            color: #64748b;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .btn-retry {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            transition: transform 0.2s;
            margin-top: 20px;
        }
        .btn-retry:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">❌</div>
        <h1>Error en el Pago</h1>
        <p>Lo sentimos, hubo un problema procesando su pago.</p>
        <p>No se realizó ningún cargo a su tarjeta.</p>
        
        <a href="/contrato" class="btn-retry">🔄 Intentar Nuevamente</a>
    </div>
</body>
</html>
