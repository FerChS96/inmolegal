# Mapeo de Variables: template.html → Laravel/Base de Datos

Este documento mapea las variables de placeholder del template.html (formato `{{variable}}`) a los campos correspondientes en la base de datos y Laravel.

## Resumen de Variables en template.html

### Variables Identificadas (24 únicas):
1. `{{inmueble1}}` - Tipo de inmueble en mayúsculas para el título
2. `{{nombre del arrendador}}` - Nombre completo del arrendador
3. `{{curp del arrendador}}` - CURP del arrendador
4. `{{el arrendador}}` - Artículo con género: "EL ARRENDADOR" / "LA ARRENDADORA"
5. `{{el arrendatario}}` - Artículo con género: "EL ARRENDATARIO" / "LA ARRENDATARIA"
6. `{{nombre del arrendatario}}` - Nombre completo del arrendatario
7. `{{curp del arrendatario}}` - CURP del arrendatario
8. `{{el fiador1}}` - Texto introductorio del fiador con artículo (si existe)
9. `{{nombre del fiador1}}` - Nombre completo del fiador con texto (si existe)
10. `{{curp del fiador}}` - CURP del fiador
11. `{{codigo estado}}` - Texto del estado: "el Estado de Jalisco" / "la Ciudad de México"
12. `{{inmueble objeto}}` - Descripción del inmueble con artículo: "una casa ubicada" / "un departamento ubicado"
13. `{{ubicacion}}` - Dirección completa del inmueble
14. `{{estado}}` - Nombre del estado (texto plano)
15. `{{objeto}}` - Uso del inmueble: "casa habitación" / "local comercial"
16. `{{precio}}` - Monto numérico de la renta (sin formato)
17. `{{precio en letra}}` - Monto en letras: "TRES MIL DOSCIENTOS"
18. `{{forma}}` - Forma de pago: "Transferencia electrónica" / "Efectivo"
19. `{{cuenta}}` - Cuenta bancaria formateada o domicilio de pago
20. `{{plazo}}` - Plazo en meses (número)
21. `{{dia}}` - Día de inicio del contrato
22. `{{mes}}` - Mes de inicio del contrato (texto)
23. `{{anio}}` - Año de inicio del contrato
24. `{{el fiador}}` - Artículo con género: "EL FIADOR" / "LA FIADORA"
25. `{{nombre del fiador}}` - Nombre completo del fiador (sin texto adicional)
26. `{{clausula fiador}}` - Cláusula completa del fiador (párrafo HTML)

---

## Mapeo Detallado

### 1. Datos del Arrendador

| Variable Template | Campo DB | Tipo | Origen | Notas |
|-------------------|----------|------|--------|-------|
| `{{nombre del arrendador}}` | `nombre_arrendador` | VARCHAR(255) | Formulario | Directo del input del form |
| `{{curp del arrendador}}` | `curp_arrendador` | VARCHAR(18) | Formulario | Directo del input del form |
| `{{el arrendador}}` | `el_arrendador` | VARCHAR(50) | **Calculado** | ContratoHelper::obtenerGeneroYArticulo() |

**Generación de `{{el arrendador}}`:**
```php
// Desde ContratoController.php
$camposCalculados['el_arrendador'] = ContratoHelper::obtenerGeneroYArticulo(
    $validated['curp_arrendador'],
    'EL ARRENDADOR',
    'LA ARRENDADORA'
);
```

---

### 2. Datos del Arrendatario

| Variable Template | Campo DB | Tipo | Origen | Notas |
|-------------------|----------|------|--------|-------|
| `{{nombre del arrendatario}}` | `nombre_arrendatario` | VARCHAR(255) | Formulario | Directo del input del form |
| `{{curp del arrendatario}}` | `curp_arrendatario` | VARCHAR(18) | Formulario | Directo del input del form |
| `{{el arrendatario}}` | `el_arrendatario` | VARCHAR(50) | **Calculado** | ContratoHelper::obtenerGeneroYArticulo() |

**Generación de `{{el arrendatario}}`:**
```php
$camposCalculados['el_arrendatario'] = ContratoHelper::obtenerGeneroYArticulo(
    $validated['curp_arrendatario'],
    'EL ARRENDATARIO',
    'LA ARRENDATARIA'
);
```

---

### 3. Datos del Fiador (Opcional)

| Variable Template | Campo DB | Tipo | Origen | Notas |
|-------------------|----------|------|--------|-------|
| `{{nombre del fiador}}` | `nombre_fiador` | VARCHAR(255) | Formulario | Solo si tiene fiador |
| `{{curp del fiador}}` | `curp_fiador` | VARCHAR(18) | Formulario | Solo si tiene fiador |
| `{{el fiador}}` | `el_fiador` | VARCHAR(50) | **Calculado** | "EL FIADOR" / "LA FIADORA" |
| `{{el fiador1}}` | `el_fiador1` | VARCHAR(100) | **Calculado** | "y como EL FIADOR " o vacío |
| `{{nombre del fiador1}}` | `nombre_fiador1` | VARCHAR(255) | **Calculado** | "JUAN PÉREZ con CURP " o vacío |
| `{{clausula fiador}}` | `clausula_fiador` | TEXT | **Calculado** | Párrafo completo HTML o vacío |

**Generación de campos del fiador:**
```php
// Si tiene_fiador = true
if ($validated['tiene_fiador']) {
    $camposCalculados['el_fiador'] = ContratoHelper::obtenerGeneroYArticulo(
        $validated['curp_fiador'],
        'EL FIADOR',
        'LA FIADORA'
    );
    
    $camposCalculados['el_fiador1'] = 'y como ' . $camposCalculados['el_fiador'] . ' ';
    
    $camposCalculados['nombre_fiador1'] = $validated['nombre_fiador'] . ' con CURP ' . $validated['curp_fiador'];
    
    $camposCalculados['clausula_fiador'] = '<p><b>Décima primera.</b> Para garantizar el cumplimiento del presente contrato, '
        . $camposCalculados['el_fiador'] . ', '
        . $validated['nombre_fiador']
        . ' se obliga de manera solidaria con ' . $camposCalculados['el_arrendatario'] . '...</p>';
} else {
    // Si no tiene fiador, todos los campos quedan vacíos
    $camposCalculados['el_fiador'] = '';
    $camposCalculados['el_fiador1'] = '';
    $camposCalculados['nombre_fiador1'] = '';
    $camposCalculados['clausula_fiador'] = '';
}
```

---

### 4. Datos del Inmueble

| Variable Template | Campo DB | Tipo | Origen | Notas |
|-------------------|----------|------|--------|-------|
| `{{inmueble1}}` | `tipo_inmueble` | VARCHAR(50) | Formulario | Transformar a MAYÚSCULAS: "CASA" / "DEPARTAMENTO" |
| `{{tipo_inmueble}}` | `tipo_inmueble` | VARCHAR(50) | Formulario | Valor original: "Casa" / "Departamento" |
| `{{inmueble objeto}}` | `inmueble_objeto` | VARCHAR(100) | **Calculado** | "una casa ubicada" / "un departamento ubicado" |
| `{{objeto}}` | `uso_inmueble` | VARCHAR(255) | Formulario | "casa habitación" / "local comercial" |
| `{{ubicacion}}` | Multiple | VARCHAR | **Calculado** | Concatenación de dirección |
| `{{estado}}` | - | VARCHAR | **Calculado** | Nombre del estado desde catálogo |
| `{{codigo estado}}` | `codigo_estado_texto` | VARCHAR(100) | **Calculado** | "el Estado de Jalisco" / "la Ciudad de México" |

**Generación de campos del inmueble:**
```php
// inmueble1: Tipo en MAYÚSCULAS
$inmueble1 = strtoupper($validated['tipo_inmueble']); // "CASA", "DEPARTAMENTO"

// inmueble_objeto: Con artículo y género
$camposCalculados['inmueble_objeto'] = ContratoHelper::obtenerInmuebleConArticulo(
    $validated['tipo_inmueble']
); // "una casa ubicada", "un departamento ubicado"

// ubicacion: Dirección completa
$ubicacion = $validated['calle'] . ' ' 
    . $validated['numero_exterior']
    . ($validated['numero_interior'] ? ' Int. ' . $validated['numero_interior'] : '')
    . ', ' . $validated['colonia']
    . ', C.P. ' . $validated['codigo_postal']
    . ', ' . $validated['ciudad'];

// codigo_estado_texto: "el Estado de X" o "la Ciudad de México"
$camposCalculados['codigo_estado_texto'] = ContratoHelper::obtenerCodigoEstado(
    $validated['codigo_estado']
); // "el Estado de Jalisco", "la Ciudad de México"

// estado: Nombre del estado (buscar en catálogo)
$estadoObj = DB::table('catalogos_estados')
    ->where('codigo', $validated['codigo_estado'])
    ->first();
$estado = $estadoObj->nombre; // "Jalisco", "Ciudad de México"
```

---

### 5. Detalles del Contrato

| Variable Template | Campo DB | Tipo | Origen | Notas |
|-------------------|----------|------|--------|-------|
| `{{precio}}` | `precio_mensual` | NUMERIC(10,2) | Formulario | Número sin formato: 3275 |
| `{{precio en letra}}` | `precio_en_letra` | VARCHAR(500) | **Calculado** | "TRES MIL DOSCIENTOS SETENTA Y CINCO" |
| `{{forma}}` | `forma_pago` | VARCHAR(50) | Formulario | "Transferencia electrónica" / "Efectivo" |
| `{{cuenta}}` | `cuenta_formateada` | VARCHAR(255) | **Calculado** | "la Cuenta CLABE 012345678901234567" o domicilio |
| `{{plazo}}` | `plazo_meses` | NUMERIC(2,0) | Formulario | Número de meses: 6, 12, 24... |
| `{{dia}}` | - | INTEGER | **Calculado** | Día extraído de fecha_inicio |
| `{{mes}}` | - | VARCHAR | **Calculado** | Mes en español: "enero", "febrero"... |
| `{{anio}}` | - | INTEGER | **Calculado** | Año extraído de fecha_inicio |

**Generación de campos del contrato:**
```php
// precio_en_letra: Convertir número a texto
$camposCalculados['precio_en_letra'] = ContratoHelper::numeroALetras(
    $validated['precio_mensual']
); // "TRES MIL DOSCIENTOS SETENTA Y CINCO"

// cuenta_formateada: Formato según tipo de pago
$camposCalculados['cuenta_formateada'] = ContratoHelper::obtenerCuentaFormateada(
    $validated['forma_pago'],
    $validated['cuenta_domicilio']
); 
// Si forma_pago = "TRANSFERENCIA": "la Cuenta CLABE 012345678901234567"
// Si forma_pago = "EFECTIVO": "el domicilio ubicado en Av. Juárez 123..."

// Extraer fecha en partes
$fechaInicio = Carbon::parse($validated['fecha_inicio']);
$dia = $fechaInicio->day; // 15
$mes = $fechaInicio->locale('es')->monthName; // "enero"
$anio = $fechaInicio->year; // 2025
```

---

## Tabla Resumen: Template → Base de Datos

| Variable Template | Campo Contrato (DB) | Tipo | Función Helper | Ejemplo Valor |
|-------------------|---------------------|------|----------------|---------------|
| `{{inmueble1}}` | `tipo_inmueble` | Direct | `strtoupper()` | "CASA" |
| `{{nombre del arrendador}}` | `nombre_arrendador` | Direct | - | "JUAN PÉREZ GARCÍA" |
| `{{curp del arrendador}}` | `curp_arrendador` | Direct | - | "PEGJ850101HDFRRN09" |
| `{{el arrendador}}` | `el_arrendador` | Calculated | `obtenerGeneroYArticulo()` | "EL ARRENDADOR" |
| `{{el arrendatario}}` | `el_arrendatario` | Calculated | `obtenerGeneroYArticulo()` | "LA ARRENDATARIA" |
| `{{nombre del arrendatario}}` | `nombre_arrendatario` | Direct | - | "MARÍA LÓPEZ SÁNCHEZ" |
| `{{curp del arrendatario}}` | `curp_arrendatario` | Direct | - | "LOSM900202MDFPNR07" |
| `{{el fiador1}}` | `el_fiador1` | Calculated | Custom logic | "y como EL FIADOR " |
| `{{nombre del fiador1}}` | `nombre_fiador1` | Calculated | Custom logic | "PEDRO GÓMEZ con CURP " |
| `{{curp del fiador}}` | `curp_fiador` | Direct | - | "GOMP800303HDFRMD01" |
| `{{codigo estado}}` | `codigo_estado_texto` | Calculated | `obtenerCodigoEstado()` | "el Estado de Jalisco" |
| `{{inmueble objeto}}` | `inmueble_objeto` | Calculated | `obtenerInmuebleConArticulo()` | "una casa ubicada" |
| `{{ubicacion}}` | Multiple fields | Concatenated | Custom logic | "Av. Juárez 123, Centro, C.P. 44100, Guadalajara" |
| `{{estado}}` | `codigo_estado` → lookup | DB Query | Join to catalogos_estados | "Jalisco" |
| `{{objeto}}` | `uso_inmueble` | Direct | - | "casa habitación" |
| `{{precio}}` | `precio_mensual` | Direct | - | "3275" |
| `{{precio en letra}}` | `precio_en_letra` | Calculated | `numeroALetras()` | "TRES MIL DOSCIENTOS SETENTA Y CINCO" |
| `{{forma}}` | `forma_pago` | Direct | - | "Transferencia electrónica" |
| `{{cuenta}}` | `cuenta_formateada` | Calculated | `obtenerCuentaFormateada()` | "la Cuenta CLABE 012345678901234567" |
| `{{plazo}}` | `plazo_meses` | Direct | - | "12" |
| `{{dia}}` | `fecha_inicio` | Date Extract | `Carbon::day` | "15" |
| `{{mes}}` | `fecha_inicio` | Date Extract | `Carbon::monthName` | "enero" |
| `{{anio}}` | `fecha_inicio` | Date Extract | `Carbon::year` | "2025" |
| `{{el fiador}}` | `el_fiador` | Calculated | `obtenerGeneroYArticulo()` | "EL FIADOR" |
| `{{nombre del fiador}}` | `nombre_fiador` | Direct | - | "PEDRO GÓMEZ MARTÍNEZ" |
| `{{clausula fiador}}` | `clausula_fiador` | Calculated | Custom logic | HTML paragraph |

---

## Implementación en el Generador de PDF

Para generar el PDF correctamente, necesitarás:

### 1. **Recuperar el Contrato de la Base de Datos**
```php
// En el controlador o servicio de generación de PDF
$contrato = Contrato::findOrFail($contratoId);
```

### 2. **Preparar los Datos para el Template**
```php
// Cargar el estado desde el catálogo
$estado = DB::table('catalogos_estados')
    ->where('codigo', $contrato->codigo_estado)
    ->first();

// Extraer fecha en partes
$fechaInicio = Carbon::parse($contrato->fecha_inicio);

// Construir ubicación completa
$ubicacion = $contrato->calle . ' ' 
    . $contrato->numero_exterior
    . ($contrato->numero_interior ? ' Int. ' . $contrato->numero_interior : '')
    . ', ' . $contrato->colonia
    . ', C.P. ' . $contrato->codigo_postal
    . ', ' . $contrato->ciudad;

// Array de reemplazo para el template
$templateData = [
    // Arrendador
    'inmueble1' => strtoupper($contrato->tipo_inmueble),
    'nombre del arrendador' => $contrato->nombre_arrendador,
    'curp del arrendador' => $contrato->curp_arrendador,
    'el arrendador' => $contrato->el_arrendador,
    
    // Arrendatario
    'el arrendatario' => $contrato->el_arrendatario,
    'nombre del arrendatario' => $contrato->nombre_arrendatario,
    'curp del arrendatario' => $contrato->curp_arrendatario,
    
    // Fiador (campos calculados, pueden estar vacíos)
    'el fiador1' => $contrato->el_fiador1,
    'nombre del fiador1' => $contrato->nombre_fiador1,
    'curp del fiador' => $contrato->curp_fiador ?? '',
    'el fiador' => $contrato->el_fiador,
    'nombre del fiador' => $contrato->nombre_fiador ?? '',
    'clausula fiador' => $contrato->clausula_fiador,
    
    // Inmueble
    'inmueble objeto' => $contrato->inmueble_objeto,
    'ubicacion' => $ubicacion,
    'estado' => $estado->nombre,
    'objeto' => $contrato->uso_inmueble,
    'codigo estado' => $contrato->codigo_estado_texto,
    
    // Detalles del contrato
    'precio' => number_format($contrato->precio_mensual, 0, '', ''),
    'precio en letra' => $contrato->precio_en_letra,
    'forma' => $contrato->forma_pago,
    'cuenta' => $contrato->cuenta_formateada,
    'plazo' => $contrato->plazo_meses,
    'dia' => $fechaInicio->day,
    'mes' => $fechaInicio->locale('es')->monthName,
    'anio' => $fechaInicio->year,
];
```

### 3. **Reemplazar Variables en el Template**
```php
// Cargar el template HTML
$templateHtml = file_get_contents(resource_path('views/contratos/template.html'));

// Reemplazar cada variable
foreach ($templateData as $key => $value) {
    $templateHtml = str_replace('{{' . $key . '}}', $value, $templateHtml);
}

// Generar PDF usando una librería como Dompdf o wkhtmltopdf
$pdf = PDF::loadHTML($templateHtml);
return $pdf->download('contrato_' . $contrato->id . '.pdf');
```

---

## Checklist de Validación

Antes de generar el PDF, verifica:

- [ ] Todos los campos calculados están presentes en la tabla `contratos`
- [ ] El script SQL `database/add_campos_calculados.sql` fue ejecutado
- [ ] `ContratoHelper.php` tiene las 6 funciones implementadas
- [ ] `ContratoController.php` calcula los 10 campos automáticamente al crear
- [ ] El template `template.html` está en `resources/views/contratos/`
- [ ] La librería PDF (Dompdf/Snappy) está instalada: `composer require barryvdh/laravel-dompdf`
- [ ] El catálogo `catalogos_estados` tiene datos correctos
- [ ] Las fechas se formatean correctamente en español con Carbon
- [ ] El campo `forma_pago` usa "TRANSFERENCIA" (no "TARJETA")

---

## Campos Faltantes por Implementar

Si el PDF no se renderiza correctamente, verifica estos campos que podrían faltar:

1. **`{{inmueble1}}`** → No está almacenado en DB, se calcula con `strtoupper(tipo_inmueble)`
2. **`{{ubicacion}}`** → No está almacenado en DB, se concatena de los campos de dirección
3. **`{{estado}}`** → No está almacenado en DB, se busca en `catalogos_estados` con JOIN
4. **`{{dia}}`, `{{mes}}`, `{{anio}}`** → No están almacenados, se extraen de `fecha_inicio`

---

## Próximos Pasos

1. **Ejecutar SQL**: Añadir los campos calculados a la tabla `contratos`
   ```bash
   psql -U postgres -d badilloDB -f database/add_campos_calculados.sql
   ```

2. **Crear Servicio de PDF**: Crear `app/Services/PdfGeneratorService.php` con la lógica de reemplazo

3. **Crear Ruta de Descarga**: Añadir ruta en `routes/web.php` para descargar PDF
   ```php
   Route::get('/contratos/{id}/pdf', [ContratoController::class, 'downloadPdf'])
       ->name('contratos.pdf');
   ```

4. **Instalar Librería PDF**: 
   ```bash
   composer require barryvdh/laravel-dompdf
   ```

5. **Probar Generación**: Crear contrato de prueba y generar PDF

---

## Notas Importantes

- **Campos Calculados**: Se guardan en la base de datos al crear el contrato, NO se calculan cada vez que se genera el PDF
- **Fiador Opcional**: Si `tiene_fiador = false`, los campos del fiador estarán vacíos pero el template los manejará correctamente
- **Formato de Fecha**: Usar `Carbon` con `locale('es')` para meses en español
- **Formato de Precio**: En `{{precio}}` va sin separadores (3275), en `{{precio en letra}}` va el texto completo
- **Forma de Pago**: El valor en DB es "TRANSFERENCIA" o "EFECTIVO", pero en el template debe mostrarse como "Transferencia electrónica" o "Efectivo"

---

**Fecha de Creación**: 19 de enero de 2025  
**Última Actualización**: 19 de enero de 2025  
**Autor**: Sistema Arrendamientos Badillo
