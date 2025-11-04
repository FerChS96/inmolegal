<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚠️ Pago Cancelado</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
        .cancel-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        h1 {
            color: #d97706;
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
        <div class="cancel-icon">⚠️</div>
        <h1>Pago Cancelado</h1>
        <p>Ha cancelado el proceso de pago.</p>
        <p>Su información se conservó. Puede reintentar cuando lo desee.</p>
        
        <a href="/contrato" class="btn-retry">🔄 Volver al Formulario</a>
    </div>
</body>
</html>
