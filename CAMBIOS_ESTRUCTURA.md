# Cambios en la Estructura del Formulario y Base de Datos

## 📋 Resumen de Cambios

Se modificó la estructura del formulario y esquema de base de datos para mejorar la normalización y consistencia de los datos.

---

## 🔄 Cambios en el Formulario (Front-End)

### 1. Separación de Nombres

**ANTES:**
- `nombre_arrendador` (campo único)
- `nombre_arrendatario` (campo único)
- `nombre_fiador` (campo único)

**AHORA:**
- **Arrendador:**
  - `nombres_arrendador`
  - `apellido_paterno_arrendador`
  - `apellido_materno_arrendador`

- **Arrendatario:**
  - `nombres_arrendatario`
  - `apellido_paterno_arrendatario`
  - `apellido_materno_arrendatario`

- **Fiador:**
  - `nombres_fiador`
  - `apellido_paterno_fiador`
  - `apellido_materno_fiador`

### 2. Catálogo de Estados

**ANTES:**
```html
<option value="CIUDAD DE MEXICO">Ciudad de México</option>
```

**AHORA:**
```html
<option value="CDMX">Ciudad de México</option>
```

El valor ahora es el **código del estado** según `catalogos_estados.codigo`

### 3. Dirección del Inmueble Separada

**ANTES:**
```html
<input name="ubicacion" placeholder="Calle, número, colonia, CP">
```

**AHORA:**
```html
<input name="calle" placeholder="Calle">
<input name="numero_exterior" placeholder="Número Exterior">
<input name="numero_interior" placeholder="Número Interior (opcional)">
<input name="codigo_postal" maxlength="5" placeholder="Código Postal">
<select name="colonia"></select> <!-- Se llena automáticamente -->
<input name="ciudad" readonly> <!-- Se llena automáticamente -->
<select name="estado"></select> <!-- Se selecciona automáticamente -->
```

**Integración con API de Códigos Postales (COPOMEX):**
- Al ingresar un CP de 5 dígitos, se consulta automáticamente la API
- Se llenan las colonias disponibles para ese CP
- Se autocompleta el municipio/ciudad
- Se selecciona automáticamente el estado
- API utilizada: `https://api.copomex.com/query/info_cp/{cp}?token=pruebas`

**Mapeo de códigos:**
- AGS = Aguascalientes
- BC = Baja California
- BCS = Baja California Sur
- CAMP = Campeche
- CHIS = Chiapas
- CHIH = Chihuahua
- CDMX = Ciudad de México
- COAH = Coahuila
- COL = Colima
- DGO = Durango
- GTO = Guanajuato
- GRO = Guerrero
- HGO = Hidalgo
- JAL = Jalisco
- MEX = Estado de México
- MICH = Michoacán
- MOR = Morelos
- NAY = Nayarit
- NL = Nuevo León
- OAX = Oaxaca
- PUE = Puebla
- QRO = Querétaro
- QROO = Quintana Roo
- SLP = San Luis Potosí
- SIN = Sinaloa
- SON = Sonora
- TAB = Tabasco
- TAMPS = Tamaulipas
- TLAX = Tlaxcala
- VER = Veracruz
- YUC = Yucatán
- ZAC = Zacatecas

---

## 🗄️ Cambios en el Esquema SQL (esquemaSQL.txt)

### Tabla `contratos`

#### Columnas ELIMINADAS:
```sql
-- ❌ ANTES
nombre_arrendador CHARACTER VARYING(255)
nombre_arrendatario CHARACTER VARYING(255)
nombre_fiador CHARACTER VARYING(255)
idestado INTEGER
estado CHARACTER VARYING(100)
dia_inicio NUMERIC(2,0)
mes_inicio CHARACTER VARYING(20)
anio_inicio NUMERIC(4,0)
precio_mensual_letra CHARACTER VARYING(255)
ubicacion TEXT  -- Dirección completa en un solo campo
```

#### Columnas AGREGADAS:
```sql
-- ✅ AHORA

-- Arrendador
nombres_arrendador CHARACTER VARYING(100) NOT NULL
apellido_paterno_arrendador CHARACTER VARYING(100) NOT NULL
apellido_materno_arrendador CHARACTER VARYING(100) NOT NULL

-- Arrendatario
nombres_arrendatario CHARACTER VARYING(100) NOT NULL
apellido_paterno_arrendatario CHARACTER VARYING(100) NOT NULL
apellido_materno_arrendatario CHARACTER VARYING(100) NOT NULL

-- Fiador
nombres_fiador CHARACTER VARYING(100) NULL
apellido_paterno_fiador CHARACTER VARYING(100) NULL
apellido_materno_fiador CHARACTER VARYING(100) NULL

-- Estado (simplificado)
codigo_estado CHARACTER VARYING(10) NOT NULL

-- Fecha (simplificada)
fecha_inicio DATE NOT NULL

-- Dirección separada (en lugar de ubicacion TEXT)
calle CHARACTER VARYING(255) NOT NULL
numero_exterior CHARACTER VARYING(50) NOT NULL
numero_interior CHARACTER VARYING(50) NULL
colonia CHARACTER VARYING(255) NOT NULL
codigo_postal CHARACTER VARYING(5) NOT NULL
ciudad CHARACTER VARYING(255) NOT NULL

-- Cuenta puede ser NULL
cuenta_domicilio CHARACTER VARYING(255) NULL
```

#### Foreign Key Actualizada:
```sql
-- ❌ ANTES
FOREIGN KEY (idestado) REFERENCES catalogos_estados(idestado)

-- ✅ AHORA
FOREIGN KEY (codigo_estado) REFERENCES catalogos_estados(codigo)
```

---

## 📝 Tareas Pendientes (Para el Backend)

### 1. Actualizar Modelo `Contrato.php`

```php
// Actualizar $fillable con los nuevos campos:
protected $fillable = [
    'token',
    'email',
    
    // Arrendador
    'nombres_arrendador',
    'apellido_paterno_arrendador',
    'apellido_materno_arrendador',
    'curp_arrendador',
    
    // Arrendatario
    'nombres_arrendatario',
    'apellido_paterno_arrendatario',
    'apellido_materno_arrendatario',
    'curp_arrendatario',
    
    // Fiador
    'tiene_fiador',
    'nombres_fiador',
    'apellido_paterno_fiador',
    'apellido_materno_fiador',
    'curp_fiador',
    
    // Inmueble
    'tipo_inmueble',
    'uso_inmueble',
    'ubicacion',
    'codigo_estado',
    
    // Contrato
    'fecha_inicio',
    'plazo_meses',
    'precio_mensual',
    'forma_pago',
    'cuenta_domicilio',
    
    // Estado
    'form_data',
    'pagado',
    'monto_pagado',
    'fecha_pago',
    'metodo_pago',
    'pago_id',
];

protected $casts = [
    'form_data' => 'array',
    'fecha_inicio' => 'date',
    'tiene_fiador' => 'boolean',
    'pagado' => 'boolean',
];
```

### 2. Actualizar `ContratoController.php`

**Validación:**
```php
$validator = Validator::make($request->all(), [
    // Arrendador
    'nombres_arrendador' => 'required|string|max:100',
    'apellido_paterno_arrendador' => 'required|string|max:100',
    'apellido_materno_arrendador' => 'required|string|max:100',
    'curp_arrendador' => 'required|size:18|regex:/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/',
    
    // Arrendatario
    'nombres_arrendatario' => 'required|string|max:100',
    'apellido_paterno_arrendatario' => 'required|string|max:100',
    'apellido_materno_arrendatario' => 'required|string|max:100',
    'curp_arrendatario' => 'required|size:18|regex:/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/',
    
    // Email
    'email' => 'required|email',
    'email_confirmation' => 'required|email|same:email',
    
    // Fiador (condicional)
    'tiene_fiador' => 'boolean',
    'nombres_fiador' => 'nullable|required_if:tiene_fiador,true|string|max:100',
    'apellido_paterno_fiador' => 'nullable|required_if:tiene_fiador,true|string|max:100',
    'apellido_materno_fiador' => 'nullable|required_if:tiene_fiador,true|string|max:100',
    'curp_fiador' => 'nullable|required_if:tiene_fiador,true|size:18',
    
    // Inmueble
    'tipo_inmueble' => 'required|string',
    'uso_inmueble' => 'required|string',
    'ubicacion' => 'required|string',
    'estado' => 'required|string|max:10', // Ahora es código
    
    // Dirección separada
    'calle' => 'required|string|max:255',
    'numero_exterior' => 'required|string|max:50',
    'numero_interior' => 'nullable|string|max:50',
    'colonia' => 'required|string|max:255',
    'codigo_postal' => 'required|string|size:5|regex:/^[0-9]{5}$/',
    'ciudad' => 'required|string|max:255',
    'estado' => 'required|string|max:10', // Código del estado
    
    // Contrato
    'fecha_inicio' => 'required|date',
    'plazo_meses' => 'required|integer|min:1|max:48',
    'pago' => 'required|numeric|min:1',
    'forma_pago' => 'required|string',
]);
```

**Crear contrato:**
```php
$contrato = Contrato::create([
    'token' => $token,
    'email' => $request->email,
    
    // Arrendador
    'nombres_arrendador' => strtoupper($request->nombres_arrendador),
    'apellido_paterno_arrendador' => strtoupper($request->apellido_paterno_arrendador),
    'apellido_materno_arrendador' => strtoupper($request->apellido_materno_arrendador),
    'curp_arrendador' => strtoupper($request->curp_arrendador),
    
    // Arrendatario
    'nombres_arrendatario' => strtoupper($request->nombres_arrendatario),
    'apellido_paterno_arrendatario' => strtoupper($request->apellido_paterno_arrendatario),
    'apellido_materno_arrendatario' => strtoupper($request->apellido_materno_arrendatario),
    'curp_arrendatario' => strtoupper($request->curp_arrendatario),
    
    // Fiador
    'tiene_fiador' => $request->boolean('tiene_fiador'),
    'nombres_fiador' => $request->nombres_fiador ? strtoupper($request->nombres_fiador) : null,
    'apellido_paterno_fiador' => $request->apellido_paterno_fiador ? strtoupper($request->apellido_paterno_fiador) : null,
    'apellido_materno_fiador' => $request->apellido_materno_fiador ? strtoupper($request->apellido_materno_fiador) : null,
    'curp_fiador' => $request->curp_fiador ? strtoupper($request->curp_fiador) : null,
    
    // Inmueble
    'tipo_inmueble' => $request->tipo_inmueble,
    'uso_inmueble' => $request->uso_inmueble,
    'ubicacion' => $request->ubicacion,
    'codigo_estado' => $request->estado, // Ahora guarda código (CDMX, JAL, etc.)
    
    // Dirección separada
    'calle' => strtoupper($request->calle),
    'numero_exterior' => strtoupper($request->numero_exterior),
    'numero_interior' => $request->numero_interior ? strtoupper($request->numero_interior) : null,
    'colonia' => strtoupper($request->colonia),
    'codigo_postal' => $request->codigo_postal,
    'ciudad' => strtoupper($request->ciudad),
    'codigo_estado' => $request->estado,
    
    // Contrato
    'fecha_inicio' => $request->fecha_inicio,
    'plazo_meses' => $request->plazo_meses,
    'precio_mensual' => $monto,
    'forma_pago' => $request->forma_pago,
    'cuenta_domicilio' => $request->cuenta_domicilio ?? null,
    
    'form_data' => $request->except(['email_confirmation']),
    'pagado' => false,
]);
```

### 3. Actualizar Generación de Token

**ANTES:**
```php
// Usaba: nombre_arrendatario (completo)
$nombreLimpio = $this->limpiarTexto($nombreArrendatario);
```

**AHORA:**
```php
// Usar: apellido_paterno_arrendatario + nombres_arrendatario
$nombreCompleto = $request->nombres_arrendatario . ' ' . $request->apellido_paterno_arrendatario;
$nombreLimpio = $this->limpiarTexto($nombreCompleto);
```

### 4. Crear Migración para PostgreSQL

```bash
php artisan make:migration refactor_contratos_table_estructura
```

La migración debe:
1. Agregar nuevas columnas
2. Migrar datos existentes (si los hay)
3. Eliminar columnas viejas
4. Actualizar foreign keys

---

## ✅ Archivos Modificados

- ✅ `resources/views/contratos/formulario.blade.php` - Formulario actualizado
- ✅ `esquemaSQL.txt` - Esquema SQL actualizado
- ⏳ `app/Models/Contrato.php` - **PENDIENTE**
- ⏳ `app/Http/Controllers/ContratoController.php` - **PENDIENTE**
- ⏳ `database/migrations/` - **PENDIENTE**

---

## 🎯 Beneficios de los Cambios

1. ✅ **Normalización mejorada** - Nombres separados en campos individuales
2. ✅ **Consistencia con catálogos** - Estados referenciados por código
3. ✅ **Fecha simplificada** - Un solo campo DATE en lugar de día/mes/año
4. ✅ **Menor redundancia** - No se guarda el nombre completo del estado dos veces
5. ✅ **Validación más precisa** - Cada campo con su propia longitud y reglas
6. ✅ **Consultas más eficientes** - Foreign key directo a código de estado
7. ✅ **Form data JSON más limpio** - Campos bien estructurados
8. ✅ **Dirección estructurada** - Campos separados para mejor búsqueda y validación
9. ✅ **Integración con API** - Autocompletado de colonias, ciudad y estado mediante CP
10. ✅ **Mejor experiencia de usuario** - Menos errores al escribir direcciones manualmente

---

## 📌 Notas Importantes

- El formulario ahora envía **códigos de estado** (CDMX, JAL, etc.) en lugar de nombres completos
- La tabla `catalogos_estados` ya tiene los códigos correctos según el esquema
- Los campos de nombres ahora son obligatorios individualmente
- El campo `cuenta_domicilio` puede ser NULL (ya no es obligatorio)
- El campo `form_data` (JSONB) sigue guardando todos los datos del formulario como respaldo
- **API de Códigos Postales**: Se utiliza COPOMEX (https://api.copomex.com) con token de pruebas
- La dirección ahora está separada en 7 campos individuales para mejor estructuración
- El código postal valida formato de 5 dígitos numéricos
- La colonia, ciudad y estado se autocompletan al ingresar un CP válido
- El número interior es opcional (puede ser NULL)

