<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ContratosInmoLegal - Generador de Contratos de Arrendamiento</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        }
        .container {
            max-width: 960px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #d0d7e3;
            border-radius: 14px;
            box-shadow: 0 12px 32px rgba(15, 40, 72, 0.12);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(120deg, #185abc 0%, #1f7ae0 100%);
            color: white;
            padding: 36px 40px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .header-icon {
            font-size: 48px;
            background: rgba(255, 255, 255, 0.18);
            padding: 18px;
            border-radius: 50%;
        }
        .header h1 {
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .header p {
            font-size: 16px;
            opacity: 0.92;
        }
        .form-content {
            padding: 40px 45px;
            background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
        }
        fieldset {
            border: 1px solid #cfd6e4;
            border-radius: 12px;
            padding: 28px;
            margin-bottom: 32px;
            background: #ffffff;
            box-shadow: inset 0 1px 0 #e9edf5;
        }
        legend {
            font-weight: 700;
            color: #185abc;
            font-size: 18px;
            padding: 0 14px;
            background: #f8faff;
            border: 1px solid #d6ddeb;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        legend i {
            color: #1f7ae0;
        }
        .row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 22px;
            margin-bottom: 20px;
        }
        .input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        label {
            font-weight: 600;
            color: #27364a;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        label i {
            color: #2b6cb0;
        }
        input, select {
            padding: 12px 14px;
            border: 1px solid #c9d3e0;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background: #ffffff;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #1f7ae0;
            box-shadow: 0 0 0 3px rgba(31, 122, 224, 0.18);
        }
        input.error, select.error {
            border-color: #e53935;
            box-shadow: 0 0 0 3px rgba(229, 57, 53, 0.12);
        }
        .error-message {
            color: #c62828;
            font-size: 13px;
            display: none;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 18px;
        }
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
        .checkbox-group label {
            margin: 0;
            font-weight: 500;
        }
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: #1f7ae0;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
            margin-top: 20px;
        }
        .btn-submit:hover {
            background: #1558b0;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(21, 88, 176, 0.25);
        }
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .loading.show {
            display: block;
        }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        .alert.show {
            display: block;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        .logo-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-header img {
            max-width: 200px;
            height: auto;
        }
        
        /* Modal de Satisfacción */
        .satisfaction-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }
        .satisfaction-modal.show {
            display: flex;
        }
        .satisfaction-content {
            background: white;
            padding: 40px;
            border-radius: 16px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }
        .satisfaction-content h3 {
            margin: 0 0 10px 0;
            color: #667eea;
            font-size: 24px;
        }
        .satisfaction-content p {
            color: #6b7280;
            margin-bottom: 30px;
        }
        .satisfaction-options {
            display: flex;
            justify-content: center;
            gap: 30px;
        }
        .emoji-btn {
            background: none;
            border: 3px solid transparent;
            font-size: 64px;
            cursor: pointer;
            padding: 20px;
            border-radius: 50%;
            transition: all 0.3s ease;
            width: 120px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .emoji-btn:hover {
            transform: scale(1.1);
            background: #f9fafb;
        }
        .emoji-btn.sad:hover {
            border-color: #f59e0b;
        }
        .emoji-btn.happy:hover {
            border-color: #10b981;
        }
        
        /* Animación de Generando Contrato */
        .generating-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(103, 126, 234, 0.95);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.5s ease;
        }
        .generating-overlay.show {
            display: flex;
        }
        .generating-content {
            text-align: center;
            color: white;
        }
        .generating-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: pulse 2s ease-in-out infinite;
        }
        .generating-content h3 {
            font-size: 28px;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .generating-content p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        .progress-bar {
            width: 300px;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
            overflow: hidden;
            margin: 0 auto;
        }
        .progress-fill {
            width: 0%;
            height: 100%;
            background: white;
            animation: progressAnimation 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        @keyframes progressAnimation {
            0% {
                width: 0%;
                opacity: 0.5;
            }
            50% {
                opacity: 1;
            }
            100% {
                width: 100%;
                opacity: 0.5;
            }
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <i class="fa-solid fa-file-contract"></i>
            </div>
            <div>
                <h1>ContratosInmoLegal</h1>
                <p>Complete la información para preparar su contrato de arrendamiento con respaldo profesional</p>
            </div>
        </div>

        <div class="form-content">
            <div id="alert" class="alert"></div>

            <form id="contratoForm">
                <!-- DATOS DEL ARRENDADOR -->
                <fieldset>
                    <legend><i class="fa-solid fa-user-tie"></i> Datos del Arrendador (Propietario)</legend>
                    <div class="row">
                        <div class="input-group">
                            <label for="nombres_arrendador"><i class="fa-solid fa-user"></i> Nombre(s) *</label>
                            <input type="text" id="nombres_arrendador" name="nombres_arrendador" required>
                            <span class="error-message">Este campo es obligatorio</span>
                        </div>
                        <div class="input-group">
                            <label for="apellido_paterno_arrendador"><i class="fa-solid fa-signature"></i> Apellido Paterno *</label>
                            <input type="text" id="apellido_paterno_arrendador" name="apellido_paterno_arrendador" required>
                            <span class="error-message">Este campo es obligatorio</span>
                        </div>
                        <div class="input-group">
                            <label for="apellido_materno_arrendador"><i class="fa-solid fa-signature"></i> Apellido Materno *</label>
                            <input type="text" id="apellido_materno_arrendador" name="apellido_materno_arrendador" required>
                            <span class="error-message">Este campo es obligatorio</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group">
                            <label for="curp_arrendador"><i class="fa-solid fa-address-card"></i> CURP (18 caracteres) *</label>
                            <input type="text" id="curp_arrendador" name="curp_arrendador" maxlength="18" required>
                            <span class="error-message">El CURP debe tener 18 caracteres</span>
                        </div>
                    </div>
                </fieldset>

                <!-- DATOS DEL ARRENDATARIO -->
                <fieldset>
                    <legend><i class="fa-solid fa-people-roof"></i> Datos del Arrendatario (Inquilino)</legend>
                    <div class="row">
                        <div class="input-group">
                            <label for="nombres_arrendatario"><i class="fa-solid fa-user"></i> Nombre(s) *</label>
                            <input type="text" id="nombres_arrendatario" name="nombres_arrendatario" required>
                            <span class="error-message">Este campo es obligatorio</span>
                        </div>
                        <div class="input-group">
                            <label for="apellido_paterno_arrendatario"><i class="fa-solid fa-signature"></i> Apellido Paterno *</label>
                            <input type="text" id="apellido_paterno_arrendatario" name="apellido_paterno_arrendatario" required>
                            <span class="error-message">Este campo es obligatorio</span>
                        </div>
                        <div class="input-group">
                            <label for="apellido_materno_arrendatario"><i class="fa-solid fa-signature"></i> Apellido Materno *</label>
                            <input type="text" id="apellido_materno_arrendatario" name="apellido_materno_arrendatario" required>
                            <span class="error-message">Este campo es obligatorio</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group">
                            <label for="curp_arrendatario"><i class="fa-solid fa-address-card"></i> CURP (18 caracteres) *</label>
                            <input type="text" id="curp_arrendatario" name="curp_arrendatario" maxlength="18" required>
                            <span class="error-message">El CURP debe tener 18 caracteres</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-group">
                            <label for="email"><i class="fa-solid fa-envelope"></i> Correo Electrónico *</label>
                            <input type="email" id="email" name="email" required>
                            <span class="error-message">Ingrese un email válido</span>
                        </div>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="tiene_fiador" name="tiene_fiador">
                        <label for="tiene_fiador"><i class="fa-solid fa-user-shield"></i> ¿Cuenta con fiador/obligado solidario?</label>
                    </div>

                    <div id="datosAval" style="display: none; margin-top: 20px;">
                        <div class="row">
                            <div class="input-group">
                                <label for="nombres_fiador"><i class="fa-solid fa-user"></i> Nombre(s) del Fiador</label>
                                <input type="text" id="nombres_fiador" name="nombres_fiador">
                            </div>
                            <div class="input-group">
                                <label for="apellido_paterno_fiador"><i class="fa-solid fa-signature"></i> Apellido Paterno</label>
                                <input type="text" id="apellido_paterno_fiador" name="apellido_paterno_fiador">
                            </div>
                            <div class="input-group">
                                <label for="apellido_materno_fiador"><i class="fa-solid fa-signature"></i> Apellido Materno</label>
                                <input type="text" id="apellido_materno_fiador" name="apellido_materno_fiador">
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-group">
                                <label for="curp_fiador"><i class="fa-solid fa-address-card"></i> CURP del Fiador</label>
                                <input type="text" id="curp_fiador" name="curp_fiador" maxlength="18">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- DATOS DEL INMUEBLE -->
                <fieldset>
                    <legend><i class="fa-solid fa-building"></i> Datos del Inmueble</legend>
                    <div class="row">
                        <div class="input-group">
                            <label for="tipo_inmueble"><i class="fa-solid fa-city"></i> Tipo de Inmueble *</label>
                            <select id="tipo_inmueble" name="tipo_inmueble" required>
                                <option value="">Seleccione...</option>
                                <option value="CASA">Casa</option>
                                <option value="DEPARTAMENTO">Departamento</option>
                                <option value="LOCAL COMERCIAL">Local Comercial</option>
                                <option value="OFICINA">Oficina</option>
                                <option value="BODEGA">Bodega</option>
                                <option value="TERRENO">Terreno</option>
                            </select>
                            <span class="error-message">Seleccione una opción</span>
                        </div>
                        <div class="input-group" id="uso_inmueble_group">
                            <label for="uso_inmueble"><i class="fa-solid fa-briefcase"></i> Uso del Inmueble *</label>
                            <input type="text" id="uso_inmueble" name="uso_inmueble" required readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                            <span class="error-message">Este campo es obligatorio</span>
                        </div>
                    </div>

                    <!-- Dirección del Inmueble -->
                    <div class="row">
                        <div class="input-group">
                            <label for="calle"><i class="fa-solid fa-road"></i> Calle *</label>
                            <input type="text" id="calle" name="calle" required>
                            <span class="error-message">Este campo es obligatorio</span>
                        </div>
                        <div class="input-group">
                            <label for="numero_exterior"><i class="fa-solid fa-hashtag"></i> Número Exterior *</label>
                            <input type="text" id="numero_exterior" name="numero_exterior" required>
                            <span class="error-message">Este campo es obligatorio</span>
                        </div>
                        <div class="input-group">
                            <label for="numero_interior"><i class="fa-solid fa-door-open"></i> Número Interior</label>
                            <input type="text" id="numero_interior" name="numero_interior" placeholder="Opcional">
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group">
                            <label for="codigo_postal"><i class="fa-solid fa-envelope"></i> Código Postal *</label>
                            <input type="text" id="codigo_postal" name="codigo_postal" maxlength="5" required>
                            <span class="error-message">Ingrese un CP de 5 dígitos</span>
                        </div>
                        <div class="input-group">
                            <label for="colonia"><i class="fa-solid fa-map-pin"></i> Colonia *</label>
                            <select id="colonia" name="colonia" required disabled>
                                <option value="">Primero ingrese el CP</option>
                            </select>
                            <span class="error-message">Seleccione una colonia</span>
                        </div>
                        <div class="input-group">
                            <label for="ciudad"><i class="fa-solid fa-city"></i> Municipio/Ciudad *</label>
                            <input type="text" id="ciudad" name="ciudad" required>
                            <span class="error-message">Este campo es obligatorio</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group">
                            <label for="estado"><i class="fa-solid fa-flag"></i> Estado *</label>
                            <select id="estado" name="estado" required>
                                <option value="">Seleccione...</option>
                                <option value="AGS">Aguascalientes</option>
                                <option value="BC">Baja California</option>
                                <option value="BCS">Baja California Sur</option>
                                <option value="CAMP">Campeche</option>
                                <option value="CHIS">Chiapas</option>
                                <option value="CHIH">Chihuahua</option>
                                <option value="CDMX">Ciudad de México</option>
                                <option value="COAH">Coahuila</option>
                                <option value="COL">Colima</option>
                                <option value="DGO">Durango</option>
                                <option value="GTO">Guanajuato</option>
                                <option value="GRO">Guerrero</option>
                                <option value="HGO">Hidalgo</option>
                                <option value="JAL">Jalisco</option>
                                <option value="MEX">Estado de México</option>
                                <option value="MICH">Michoacán</option>
                                <option value="MOR">Morelos</option>
                                <option value="NAY">Nayarit</option>
                                <option value="NL">Nuevo León</option>
                                <option value="OAX">Oaxaca</option>
                                <option value="PUE">Puebla</option>
                                <option value="QRO">Querétaro</option>
                                <option value="QROO">Quintana Roo</option>
                                <option value="SLP">San Luis Potosí</option>
                                <option value="SIN">Sinaloa</option>
                                <option value="SON">Sonora</option>
                                <option value="TAB">Tabasco</option>
                                <option value="TAMPS">Tamaulipas</option>
                                <option value="TLAX">Tlaxcala</option>
                                <option value="VER">Veracruz</option>
                                <option value="YUC">Yucatán</option>
                                <option value="ZAC">Zacatecas</option>
                            </select>
                            <span class="error-message">Seleccione un estado</span>
                        </div>
                    </div>
                </fieldset>

                <!-- CONDICIONES DEL ARRENDAMIENTO -->
                <fieldset>
                    <legend><i class="fa-solid fa-file-invoice-dollar"></i> Condiciones del Arrendamiento</legend>
                    <div class="row">
                        <div class="input-group">
                            <label for="fecha_inicio"><i class="fa-solid fa-calendar-days"></i> Fecha de Inicio *</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                            <span class="error-message">Seleccione una fecha válida</span>
                        </div>
                        <div class="input-group">
                            <label for="plazo_meses"><i class="fa-solid fa-hourglass-half"></i> Plazo (meses) *</label>
                            <input type="number" id="plazo_meses" name="plazo_meses" min="1" max="48" required>
                            <span class="error-message">Máximo 48 meses</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group">
                            <label for="pago"><i class="fa-solid fa-dollar-sign"></i> Pago (MXN) *</label>
                            <input type="number" id="pago" name="pago" min="1" step="0.01" required>
                            <span class="error-message">Ingrese un monto válido</span>
                        </div>
                        <div class="input-group">
                            <label for="forma_pago"><i class="fa-solid fa-money-check-dollar"></i> Forma de Pago *</label>
                            <select id="forma_pago" name="forma_pago" required>
                                <option value="">Seleccione...</option>
                                <option value="EFECTIVO">Efectivo</option>
                                <option value="TRANSFERENCIA">Transferencia electrónica</option>
                            </select>
                            <span class="error-message">Seleccione una forma de pago</span>
                        </div>
                    </div>

                    <!-- Campo dinámico: Cuenta CLABE o Domicilio -->
                    <div class="row" id="cuenta_domicilio_container" style="display: none;">
                        <div class="input-group">
                            <label for="cuenta_domicilio" id="cuenta_domicilio_label">
                                <i class="fa-solid fa-building-columns"></i> Cuenta CLABE *
                            </label>
                            <input type="text" id="cuenta_domicilio" name="cuenta_domicilio" maxlength="255">
                            <span class="error-message" id="cuenta_domicilio_error">Este campo es requerido</span>
                        </div>
                    </div>
                </fieldset>

                <button type="button" class="btn-submit" id="btnSubmit">
                    <i class="fa-solid fa-file-contract"></i> Generar Contrato
                </button>
            </form>

            <!-- Animación de Generando Contrato -->
            <div class="generating-overlay" id="generatingOverlay">
                <div class="generating-content">
                    <div class="generating-icon">
                        <i class="fa-solid fa-file-contract fa-bounce"></i>
                    </div>
                    <h3>Generando tu contrato</h3>
                    <p>Por favor espera un momento...</p>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                </div>
            </div>

            <!-- Modal de Encuesta de Satisfacción -->
            <div class="satisfaction-modal" id="satisfactionModal">
                <div class="satisfaction-content">
                    <h3>¿Cómo fue tu experiencia con el formulario?</h3>
                    <p>Tu opinión nos ayuda a mejorar</p>
                    <div class="satisfaction-options">
                        <button type="button" class="emoji-btn sad" id="btnSad" title="Necesita mejorar">
                            😔
                        </button>
                        <button type="button" class="emoji-btn happy" id="btnHappy" title="¡Excelente!">
                            😊
                        </button>
                    </div>
                </div>
            </div>

            <div class="loading" id="loading">
                <p><i class="fa-solid fa-spinner fa-spin"></i> Procesando su solicitud...</p>
            </div>
        </div>
    </div>

    <script>
        // Configuración - Usar url() de Laravel para generar la URL correcta con subdirectorio
        const API_URL = '{{ url("/api/contrato") }}';
        console.log('API_URL configurada:', API_URL);
        const fechaInicioInput = document.getElementById('fecha_inicio');
        const today = new Date().toISOString().split('T')[0];
        fechaInicioInput.value = today;
        fechaInicioInput.min = today;

        // Toggle fiador
        document.getElementById('tiene_fiador').addEventListener('change', function() {
            document.getElementById('datosAval').style.display = this.checked ? 'block' : 'none';
            if (!this.checked) {
                document.getElementById('nombres_fiador').value = '';
                document.getElementById('apellido_paterno_fiador').value = '';
                document.getElementById('apellido_materno_fiador').value = '';
                document.getElementById('curp_fiador').value = '';
            }
        });

        // ============================================
        // API DE CÓDIGOS POSTALES (Zippopotam - Gratuita)
        // ============================================
        const codigoPostalInput = document.getElementById('codigo_postal');
        const coloniaSelect = document.getElementById('colonia');
        const ciudadInput = document.getElementById('ciudad');
        const estadoSelect = document.getElementById('estado');

        codigoPostalInput.addEventListener('input', async function() {
            // Solo números
            this.value = this.value.replace(/[^0-9]/g, '');
            
            if (this.value.length === 5) {
                try {
                    // API gratuita de Zippopotam para códigos postales de México
                    const response = await fetch(`https://api.zippopotam.us/mx/${this.value}`);
                    
                    if (!response.ok) throw new Error('CP no encontrado');
                    
                    const data = await response.json();
                    
                    if (data.places && data.places.length > 0) {
                        // Limpiar y llenar colonias
                        coloniaSelect.innerHTML = '<option value="">Seleccione una colonia...</option>';
                        
                        // Obtener todas las colonias (places) para este CP
                        data.places.forEach(place => {
                            const option = document.createElement('option');
                            option.value = place['place name'];
                            option.textContent = place['place name'];
                            coloniaSelect.appendChild(option);
                        });
                        coloniaSelect.disabled = false;
                        
                        // Usar el primer lugar para obtener estado
                        const firstPlace = data.places[0];
                        
                        // Dejar el campo de ciudad vacío o con placeholder para que el usuario lo complete
                        // La API no siempre tiene el municipio correcto
                        if (ciudadInput.value === '') {
                            ciudadInput.placeholder = 'Ingrese su municipio';
                            ciudadInput.focus();
                        }
                        
                        // Seleccionar estado automáticamente usando el código que ya viene en la API
                        if (firstPlace['state abbreviation']) {
                            const codigoEstado = firstPlace['state abbreviation'];
                            estadoSelect.value = codigoEstado;
                        }
                        
                        // Quitar error
                        this.classList.remove('error');
                        const errorSpan = this.parentElement.querySelector('.error-message');
                        if (errorSpan) errorSpan.style.display = 'none';
                        
                    } else {
                        throw new Error('CP no válido');
                    }
                } catch (error) {
                    console.error('Error al buscar CP:', error);
                    coloniaSelect.innerHTML = '<option value="">CP no encontrado</option>';
                    coloniaSelect.disabled = true;
                    ciudadInput.value = '';
                    
                    this.classList.add('error');
                    const errorSpan = this.parentElement.querySelector('.error-message');
                    if (errorSpan) {
                        errorSpan.textContent = 'Código postal no válido';
                        errorSpan.style.display = 'block';
                    }
                }
            } else {
                // Resetear si no tiene 5 dígitos
                coloniaSelect.innerHTML = '<option value="">Primero ingrese el CP</option>';
                coloniaSelect.disabled = true;
                ciudadInput.value = '';
            }
        });

        // Validación CURP en tiempo real
        const curpRegex = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/;
        
        ['curp_arrendador', 'curp_arrendatario', 'curp_fiador'].forEach(id => {
            const input = document.getElementById(id);
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                
                const errorSpan = this.parentElement.querySelector('.error-message');
                
                if (this.value.length === 18) {
                    if (curpRegex.test(this.value)) {
                        this.classList.remove('error');
                        if (errorSpan) errorSpan.style.display = 'none';
                    } else {
                        this.classList.add('error');
                        if (errorSpan) {
                            errorSpan.textContent = 'Formato de CURP inválido';
                            errorSpan.style.display = 'block';
                        }
                    }
                } else if (this.value.length > 0) {
                    this.classList.remove('error');
                    if (errorSpan) errorSpan.style.display = 'none';
                }
            });
            
            input.addEventListener('blur', function() {
                const errorSpan = this.parentElement.querySelector('.error-message');
                
                if (this.value.length > 0 && this.value.length !== 18) {
                    this.classList.add('error');
                    if (errorSpan) {
                        errorSpan.textContent = 'El CURP debe tener 18 caracteres';
                        errorSpan.style.display = 'block';
                    }
                } else if (this.value.length === 18 && !curpRegex.test(this.value)) {
                    this.classList.add('error');
                    if (errorSpan) {
                        errorSpan.textContent = 'Formato de CURP inválido';
                        errorSpan.style.display = 'block';
                    }
                }
            });
        });

        // ============================================
        // CAMPO DINÁMICO CUENTA/DOMICILIO SEGÚN FORMA DE PAGO
        // ============================================
        const formaPagoSelect = document.getElementById('forma_pago');
        const cuentaDomicilioContainer = document.getElementById('cuenta_domicilio_container');
        const cuentaDomicilioInput = document.getElementById('cuenta_domicilio');
        const cuentaDomicilioLabel = document.getElementById('cuenta_domicilio_label');
        const cuentaDomicilioError = document.getElementById('cuenta_domicilio_error');

        formaPagoSelect.addEventListener('change', function() {
            const formaPago = this.value;
            
            if (formaPago === 'TRANSFERENCIA') {
                // Mostrar campo para Cuenta CLABE
                cuentaDomicilioContainer.style.display = 'block';
                cuentaDomicilioLabel.innerHTML = '<i class="fa-solid fa-building-columns"></i> Cuenta CLABE *';
                cuentaDomicilioInput.placeholder = 'Ingrese 18 dígitos de la CLABE';
                cuentaDomicilioInput.maxLength = 18;
                cuentaDomicilioInput.pattern = '[0-9]{18}';
                cuentaDomicilioInput.required = true;
                cuentaDomicilioInput.value = '';
                cuentaDomicilioError.textContent = 'Ingrese una CLABE válida de 18 dígitos';
                
                // Validación en tiempo real para CLABE (solo números, 18 dígitos)
                cuentaDomicilioInput.removeEventListener('input', validarDomicilio);
                cuentaDomicilioInput.addEventListener('input', validarCLABE);
                
            } else if (formaPago === 'EFECTIVO') {
                // Mostrar campo para Domicilio
                cuentaDomicilioContainer.style.display = 'block';
                cuentaDomicilioLabel.innerHTML = '<i class="fa-solid fa-location-dot"></i> Domicilio para Pago *';
                cuentaDomicilioInput.placeholder = 'Ej: Calle Principal #123, Col. Centro';
                cuentaDomicilioInput.maxLength = 255;
                cuentaDomicilioInput.removeAttribute('pattern');
                cuentaDomicilioInput.required = true;
                cuentaDomicilioInput.value = '';
                cuentaDomicilioError.textContent = 'Ingrese el domicilio donde se realizará el pago';
                
                // Validación en tiempo real para domicilio
                cuentaDomicilioInput.removeEventListener('input', validarCLABE);
                cuentaDomicilioInput.addEventListener('input', validarDomicilio);
                
            } else {
                // Ocultar campo si no hay forma de pago seleccionada
                cuentaDomicilioContainer.style.display = 'none';
                cuentaDomicilioInput.required = false;
                cuentaDomicilioInput.value = '';
            }
            
            // Limpiar estado de error
            cuentaDomicilioInput.classList.remove('error');
            cuentaDomicilioError.style.display = 'none';
        });

        // Función de validación para CLABE
        function validarCLABE() {
            const input = cuentaDomicilioInput;
            const errorSpan = cuentaDomicilioError;
            
            // Solo números
            input.value = input.value.replace(/[^0-9]/g, '');
            
            if (input.value.length > 0) {
                if (input.value.length === 18) {
                    input.classList.remove('error');
                    errorSpan.style.display = 'none';
                } else {
                    input.classList.add('error');
                    errorSpan.textContent = `Faltan ${18 - input.value.length} dígitos`;
                    errorSpan.style.display = 'block';
                }
            } else {
                input.classList.remove('error');
                errorSpan.style.display = 'none';
            }
        }

        // Función de validación para Domicilio
        function validarDomicilio() {
            const input = cuentaDomicilioInput;
            const errorSpan = cuentaDomicilioError;
            
            if (input.value.length > 0) {
                if (input.value.length >= 10) {
                    input.classList.remove('error');
                    errorSpan.style.display = 'none';
                } else {
                    input.classList.add('error');
                    errorSpan.textContent = 'El domicilio debe tener al menos 10 caracteres';
                    errorSpan.style.display = 'block';
                }
            } else {
                input.classList.remove('error');
                errorSpan.style.display = 'none';
            }
        }

        // ============================================
        // ENCUESTA DE SATISFACCIÓN Y SUBMIT
        // ============================================
        const btnSubmit = document.getElementById('btnSubmit');
        const satisfactionModal = document.getElementById('satisfactionModal');
        const generatingOverlay = document.getElementById('generatingOverlay');
        const btnSad = document.getElementById('btnSad');
        const btnHappy = document.getElementById('btnHappy');
        const contratoForm = document.getElementById('contratoForm');

        // Al hacer click en "Generar Contrato"
        btnSubmit.addEventListener('click', function(e) {
            e.preventDefault();

            // Validar formulario primero
            if (!validateForm()) {
                return;
            }

            // Mostrar animación de "Generando contrato"
            generatingOverlay.classList.add('show');

            // Después de 2 segundos, mostrar encuesta de satisfacción
            setTimeout(() => {
                generatingOverlay.classList.remove('show');
                satisfactionModal.classList.add('show');
            }, 2000);
        });

        // Función de validación
        function validateForm() {
            let valid = true;
            const requiredFields = contratoForm.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                const errorSpan = field.parentElement.querySelector('.error-message');
                const value = field.value;

                if (!value) {
                    field.classList.add('error');
                    if (errorSpan) errorSpan.style.display = 'block';
                    valid = false;
                } else {
                    field.classList.remove('error');
                    if (errorSpan) errorSpan.style.display = 'none';
                }
            });

            // Validación específica para cuenta_domicilio según forma de pago
            const formaPago = formaPagoSelect.value;
            const cuentaDomicilio = cuentaDomicilioInput.value;
            
            if (formaPago === 'TRANSFERENCIA' && cuentaDomicilio) {
                // Validar que sea una CLABE de 18 dígitos
                if (!/^[0-9]{18}$/.test(cuentaDomicilio)) {
                    cuentaDomicilioInput.classList.add('error');
                    cuentaDomicilioError.textContent = 'La CLABE debe tener exactamente 18 dígitos';
                    cuentaDomicilioError.style.display = 'block';
                    valid = false;
                }
            } else if (formaPago === 'EFECTIVO' && cuentaDomicilio) {
                // Validar que el domicilio tenga al menos 10 caracteres
                if (cuentaDomicilio.length < 10) {
                    cuentaDomicilioInput.classList.add('error');
                    cuentaDomicilioError.textContent = 'El domicilio debe tener al menos 10 caracteres';
                    cuentaDomicilioError.style.display = 'block';
                    valid = false;
                }
            }

            if (!valid) {
                showAlert('⚠️ Por favor complete todos los campos obligatorios', 'error');
            }

            return valid;
        }

        // Al hacer click en un emoji, proceder al pago
        btnSad.addEventListener('click', function() {
            proceedToPayment();
        });

        btnHappy.addEventListener('click', function() {
            proceedToPayment();
        });

        // Función para proceder al pago
        async function proceedToPayment() {
            // Ocultar modal
            satisfactionModal.classList.remove('show');
            
            // Mostrar loading
            document.getElementById('btnSubmit').disabled = true;
            document.getElementById('loading').classList.add('show');

            // Preparar datos del formulario
            const formData = new FormData(contratoForm);
            const data = Object.fromEntries(formData.entries());
            
            // Asegurar que tiene_fiador se envíe como boolean
            data.tiene_fiador = document.getElementById('tiene_fiador').checked;

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                // Verificar el tipo de contenido de la respuesta
                const contentType = response.headers.get('content-type');
                console.log('Response status:', response.status);
                console.log('Content-Type:', contentType);

                if (!contentType || !contentType.includes('application/json')) {
                    // La respuesta no es JSON, probablemente HTML de error
                    const htmlText = await response.text();
                    console.error('Respuesta HTML recibida (esperaba JSON):', htmlText.substring(0, 500));
                    throw new Error('El servidor devolvió HTML en lugar de JSON. Verifica la configuración de rutas.');
                }

                const result = await response.json();
                console.log('Result:', result);

                if (result.success) {
                    showAlert('✅ Formulario procesado. Redirigiendo al pago...', 'success');
                    
                    // Redirigir a la página de pago de Clip
                    setTimeout(() => {
                        window.location.href = result.redirect_payment;
                    }, 1500);
                } else {
                    // Mostrar errores de validación detallados
                    let errorMessage = 'Error al procesar el formulario:';
                    
                    if (result.errors) {
                        errorMessage += '\n';
                        Object.keys(result.errors).forEach(key => {
                            errorMessage += `\n- ${result.errors[key][0]}`;
                        });
                    } else if (result.message) {
                        errorMessage += ' ' + result.message;
                    }
                    
                    showAlert('❌ ' + errorMessage, 'error');
                    console.error('Errores de validación:', result);
                    document.getElementById('btnSubmit').disabled = false;
                    document.getElementById('loading').classList.remove('show');
                }
            } catch (error) {
                console.error('Error completo:', error);
                showAlert('❌ Error de conexión: ' + error.message, 'error');
                document.getElementById('btnSubmit').disabled = false;
                document.getElementById('loading').classList.remove('show');
            }
        }

        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.textContent = message;
            alert.className = `alert alert-${type} show`;
            
            setTimeout(() => {
                alert.classList.remove('show');
            }, 5000);
        }

        // ============================================
        // CONVERTIR NOMBRES Y APELLIDOS A MAYÚSCULAS
        // ============================================
        const camposNombres = [
            'nombres_arrendador',
            'apellido_paterno_arrendador',
            'apellido_materno_arrendador',
            'nombres_arrendatario',
            'apellido_paterno_arrendatario',
            'apellido_materno_arrendatario',
            'nombres_fiador',
            'apellido_paterno_fiador',
            'apellido_materno_fiador'
        ];

        camposNombres.forEach(campoId => {
            const campo = document.getElementById(campoId);
            if (campo) {
                // Convertir a mayúsculas mientras se escribe
                campo.addEventListener('input', function() {
                    const start = this.selectionStart;
                    const end = this.selectionEnd;
                    this.value = this.value.toUpperCase();
                    this.setSelectionRange(start, end);
                });
            }
        });

        // ============================================
        // LÓGICA DE USO DEL INMUEBLE
        // ============================================
        const tipoInmuebleSelect = document.getElementById('tipo_inmueble');
        const usoInmuebleInput = document.getElementById('uso_inmueble');

        tipoInmuebleSelect.addEventListener('change', function() {
            const tipoSeleccionado = this.value;
            
            if (tipoSeleccionado === 'CASA' || tipoSeleccionado === 'DEPARTAMENTO') {
                // Para casa o departamento: uso predeterminado "VIVIENDA" (readonly)
                usoInmuebleInput.value = 'VIVIENDA';
                usoInmuebleInput.readOnly = true;
                usoInmuebleInput.style.backgroundColor = '#f5f5f5';
                usoInmuebleInput.style.cursor = 'not-allowed';
            } else if (tipoSeleccionado) {
                // Para otros tipos: permitir escritura manual
                usoInmuebleInput.value = '';
                usoInmuebleInput.readOnly = false;
                usoInmuebleInput.style.backgroundColor = '';
                usoInmuebleInput.style.cursor = '';
                usoInmuebleInput.placeholder = 'Ingrese el uso del inmueble';
                
                // Convertir a mayúsculas mientras escribe
                usoInmuebleInput.addEventListener('input', function() {
                    const start = this.selectionStart;
                    const end = this.selectionEnd;
                    this.value = this.value.toUpperCase();
                    this.setSelectionRange(start, end);
                });
            } else {
                // Si no hay selección, limpiar y deshabilitar
                usoInmuebleInput.value = '';
                usoInmuebleInput.readOnly = true;
                usoInmuebleInput.style.backgroundColor = '#f5f5f5';
                usoInmuebleInput.style.cursor = 'not-allowed';
            }
        });
    </script>
</body>
</html>
