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
                        <div class="input-group">
                            <label for="email_confirmation"><i class="fa-solid fa-envelope-circle-check"></i> Confirmar Correo *</label>
                            <input type="email" id="email_confirmation" name="email_confirmation" required>
                            <span class="error-message">El correo debe coincidir</span>
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
                        <div class="input-group">
                            <label for="uso_inmueble"><i class="fa-solid fa-briefcase"></i> Uso del Inmueble *</label>
                            <select id="uso_inmueble" name="uso_inmueble" required>
                                <option value="">Seleccione...</option>
                                <option value="HABITACIONAL">Habitacional</option>
                                <option value="COMERCIAL">Comercial</option>
                                <option value="INDUSTRIAL">Industrial</option>
                                <option value="MIXTO">Mixto</option>
                            </select>
                            <span class="error-message">Seleccione una opción</span>
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
                                <option value="TARJETA">Tarjeta de Crédito/Débito</option>
                            </select>
                            <span class="error-message">Seleccione una forma de pago</span>
                        </div>
                    </div>
                </fieldset>

                <button type="submit" class="btn-submit" id="btnSubmit">
                    <i class="fa-solid fa-credit-card"></i> Proceder al Pago
                </button>
            </form>

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

        // Submit del formulario
        document.getElementById('contratoForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validaciones
            let valid = true;
            const requiredFields = this.querySelectorAll('[required]');
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

            const emailField = document.getElementById('email');
            const emailConfirmationField = document.getElementById('email_confirmation');
            const emailConfirmError = emailConfirmationField.parentElement.querySelector('.error-message');

            if (emailField.value && emailConfirmationField.value && emailField.value !== emailConfirmationField.value) {
                emailConfirmationField.classList.add('error');
                if (emailConfirmError) {
                    emailConfirmError.textContent = 'Los correos deben coincidir';
                    emailConfirmError.style.display = 'block';
                }
                valid = false;
            } else {
                emailConfirmationField.classList.remove('error');
                if (emailConfirmError) {
                    emailConfirmError.style.display = 'none';
                }
            }

            const pagoField = document.getElementById('pago');
            if (parseFloat(pagoField.value) <= 0) {
                pagoField.classList.add('error');
                const errorSpan = pagoField.parentElement.querySelector('.error-message');
                if (errorSpan) errorSpan.style.display = 'block';
                valid = false;
            }

            // Validar CURP con formato oficial mexicano
            const curpArrendador = document.getElementById('curp_arrendador').value;
            const curpArrendatario = document.getElementById('curp_arrendatario').value;
            const curpRegex = /^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/;
            
            if (curpArrendador.length !== 18 || !curpRegex.test(curpArrendador)) {
                const campoArrendador = document.getElementById('curp_arrendador');
                campoArrendador.classList.add('error');
                const errorSpan = campoArrendador.parentElement.querySelector('.error-message');
                if (errorSpan) {
                    errorSpan.textContent = curpArrendador.length !== 18 ? 'El CURP debe tener 18 caracteres' : 'Formato de CURP inválido';
                    errorSpan.style.display = 'block';
                }
                valid = false;
            }
            
            if (curpArrendatario.length !== 18 || !curpRegex.test(curpArrendatario)) {
                const campoArrendatario = document.getElementById('curp_arrendatario');
                campoArrendatario.classList.add('error');
                const errorSpan = campoArrendatario.parentElement.querySelector('.error-message');
                if (errorSpan) {
                    errorSpan.textContent = curpArrendatario.length !== 18 ? 'El CURP debe tener 18 caracteres' : 'Formato de CURP inválido';
                    errorSpan.style.display = 'block';
                }
                valid = false;
            }

            if (!valid) {
                showAlert('Por favor complete todos los campos requeridos correctamente', 'error');
                return;
            }

            // Mostrar loading
            document.getElementById('btnSubmit').disabled = true;
            document.getElementById('loading').classList.add('show');

            // Preparar datos
            const formData = new FormData(this);
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
        });

        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.textContent = message;
            alert.className = `alert alert-${type} show`;
            
            setTimeout(() => {
                alert.classList.remove('show');
            }, 5000);
        }
    </script>
</body>
</html>
