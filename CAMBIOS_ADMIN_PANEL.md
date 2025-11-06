# Actualización del Panel de Administración - Contratos InmoLegal

**Fecha**: 19 de enero de 2025  
**Autor**: Sistema Arrendamientos Badillo

## Resumen de Cambios

Se actualizó el panel de administración para reflejar la nueva estructura de la base de datos con los campos calculados y la información actualizada de los contratos.

---

## 1. Vista de Lista de Contratos (`admin/contratos.blade.php`)

### Cambios en la Tabla Principal

**Columnas Anteriores:**
- Token
- Arrendatario (con 3 campos concatenados)
- Email
- Inmueble (dirección simple)
- Renta Mensual
- Estado Pago
- Fecha Creación

**Columnas Nuevas:**
- Token
- Arrendatario (campo único `nombre_arrendatario`)
- **Tipo Inmueble** (nuevo)
- Ubicación (mejorada con ciudad)
- **Plazo** (nuevo - muestra meses)
- Renta Mensual
- Estado Pago
- **Fecha Inicio** (nuevo - del contrato)

### Cambios en Campos

```php
// ANTES
{{ $contrato->nombres_arrendatario }} 
{{ $contrato->apellido_paterno_arrendatario }} 
{{ $contrato->apellido_materno_arrendatario }}

// AHORA
{{ $contrato->nombre_arrendatario }}
```

```php
// ANTES
@if($contrato->fecha_pago)

// AHORA
@if($contrato->pagado)
```

### Nuevo Filtro: Tipo de Inmueble

Se agregó un selector para filtrar por tipo de inmueble:
- Casa
- Departamento
- Local Comercial
- Oficina
- Bodega
- Terreno

---

## 2. Controlador de Admin (`AdminController.php`)

### Método `contratos()` Actualizado

**Búsqueda Actualizada:**
```php
// ANTES
->orWhere('email', 'LIKE', "%{$search}%")
->orWhere('nombres_arrendatario', 'LIKE', "%{$search}%")
->orWhere('apellido_paterno_arrendatario', 'LIKE', "%{$search}%")

// AHORA
->orWhere('nombre_arrendatario', 'LIKE', "%{$search}%")
->orWhere('nombre_arrendador', 'LIKE', "%{$search}%")
->orWhere('ciudad', 'LIKE', "%{$search}%")
->orWhere('colonia', 'LIKE', "%{$search}%")
```

**Nuevo Filtro:**
```php
// Filtro por tipo de inmueble
if ($request->filled('tipo_inmueble')) {
    $query->where('tipo_inmueble', $request->tipo_inmueble);
}
```

**Estado de Pago Actualizado:**
```php
// ANTES
if ($request->estado_pago === 'pagado') {
    $query->whereNotNull('fecha_pago');
} else {
    $query->whereNull('fecha_pago');
}

// AHORA
if ($request->estado_pago === 'pagado') {
    $query->where('pagado', true);
} else {
    $query->where('pagado', false);
}
```

---

## 3. Vista de Detalle de Contrato (`admin/ver-contrato.blade.php`)

### Sección: Información del Arrendatario

**Campos Actualizados:**
```php
// ANTES
{{ $contrato->nombres_arrendatario }} 
{{ $contrato->apellido_paterno_arrendatario }} 
{{ $contrato->apellido_materno_arrendatario }}
{{ $contrato->email }}
{{ $contrato->telefono ?? 'N/A' }}

// AHORA
{{ $contrato->nombre_arrendatario }}
{{ $contrato->curp_arrendatario }}
{{ $contrato->el_arrendatario ?? 'N/A' }} // Campo calculado
```

### Sección: Información del Arrendador

**Campos Actualizados:**
```php
// ANTES
{{ $contrato->nombres_arrendador }} 
{{ $contrato->apellido_paterno_arrendador }} 
{{ $contrato->apellido_materno_arrendador }}

// AHORA
{{ $contrato->nombre_arrendador }}
{{ $contrato->curp_arrendador }}
{{ $contrato->el_arrendador ?? 'N/A' }} // Campo calculado
```

### Nueva Sección: Información del Fiador

Se agregó una sección completa para mostrar datos del fiador (si existe):

```php
@if($contrato->tiene_fiador)
<div class="section">
    <h2>Información del Fiador</h2>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Nombre Completo</div>
            <div class="info-value">{{ $contrato->nombre_fiador }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">CURP</div>
            <div class="info-value">{{ $contrato->curp_fiador }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Género (calculado)</div>
            <div class="info-value">{{ $contrato->el_fiador ?? 'N/A' }}</div>
        </div>
    </div>
</div>
@endif
```

### Sección: Inmueble (Mejorada)

**Nuevos Campos:**
- Tipo de Inmueble
- Uso del Inmueble
- Inmueble (calculado) - con artículo: "una casa ubicada"
- Dirección Completa (ya existía)

```php
<div class="info-item">
    <div class="info-label">Tipo de Inmueble</div>
    <div class="info-value">{{ ucfirst($contrato->tipo_inmueble) }}</div>
</div>
<div class="info-item">
    <div class="info-label">Uso del Inmueble</div>
    <div class="info-value">{{ $contrato->uso_inmueble }}</div>
</div>
<div class="info-item">
    <div class="info-label">Inmueble (calculado)</div>
    <div class="info-value">{{ $contrato->inmueble_objeto ?? 'N/A' }}</div>
</div>
```

### Sección: Detalles del Contrato (Ampliada)

**Nuevos Campos:**
- Precio en Letra (calculado)
- Cuenta/Domicilio de Pago
- Cuenta Formateada (calculado)
- Estado
- Estado Texto (calculado)

```php
<div class="info-item">
    <div class="info-label">Precio en Letra (calculado)</div>
    <div class="info-value">{{ $contrato->precio_en_letra ?? 'N/A' }}</div>
</div>
<div class="info-item">
    <div class="info-label">Cuenta/Domicilio de Pago</div>
    <div class="info-value">{{ $contrato->cuenta_domicilio ?? 'N/A' }}</div>
</div>
<div class="info-item">
    <div class="info-label">Cuenta Formateada (calculado)</div>
    <div class="info-value">{{ $contrato->cuenta_formateada ?? 'N/A' }}</div>
</div>
```

### Sección: Estado del Contrato (Mejorada)

**Nuevo Campo:**
- ID de Pago

```php
<div class="info-item">
    <div class="info-label">ID de Pago</div>
    <div class="info-value" style="font-family: 'Courier New', monospace; font-size: 12px;">
        {{ $contrato->pago_id ?? 'N/A' }}
    </div>
</div>
```

---

## 4. Campos Calculados Mostrados

Los siguientes campos calculados ahora se muestran en la vista de detalle:

1. **`el_arrendador`** - "EL ARRENDADOR" / "LA ARRENDADORA"
2. **`el_arrendatario`** - "EL ARRENDATARIO" / "LA ARRENDATARIA"
3. **`el_fiador`** - "EL FIADOR" / "LA FIADORA"
4. **`inmueble_objeto`** - "una casa ubicada" / "un departamento ubicado"
5. **`precio_en_letra`** - "TRES MIL DOSCIENTOS SETENTA Y CINCO"
6. **`cuenta_formateada`** - "la Cuenta CLABE 012345..." o domicilio
7. **`codigo_estado_texto`** - "el Estado de Jalisco" / "la Ciudad de México"

---

## 5. Archivos Modificados

1. **`resources/views/admin/contratos.blade.php`**
   - Actualizada tabla de contratos (8 columnas)
   - Agregado filtro de tipo de inmueble
   - Actualizado campo de arrendatario
   - Corregido colspan de "no results" a 8

2. **`app/Http/Controllers/AdminController.php`**
   - Método `contratos()` actualizado
   - Nueva lógica de búsqueda
   - Filtro por tipo de inmueble
   - Estado de pago usando campo `pagado`

3. **`resources/views/admin/ver-contrato.blade.php`**
   - Sección de arrendatario actualizada
   - Sección de arrendador actualizada
   - Nueva sección de fiador (condicional)
   - Sección de inmueble mejorada
   - Sección de detalles ampliada
   - Sección de estado mejorada

---

## 6. Compatibilidad con Estructura Anterior

⚠️ **IMPORTANTE**: Estos cambios requieren que:

1. La base de datos tenga la nueva estructura de campos
2. Se ejecute el script `database/add_campos_calculados.sql`
3. Los campos calculados estén siendo generados por el `ContratoController`

### Campos que DEBEN existir en la tabla `contratos`:

**Campos Directos:**
- `nombre_arrendatario` (VARCHAR)
- `nombre_arrendador` (VARCHAR)
- `curp_arrendatario` (VARCHAR)
- `curp_arrendador` (VARCHAR)
- `nombre_fiador` (VARCHAR, NULL)
- `curp_fiador` (VARCHAR, NULL)
- `tiene_fiador` (BOOLEAN)
- `tipo_inmueble` (VARCHAR)
- `uso_inmueble` (VARCHAR)
- `plazo_meses` (NUMERIC)
- `forma_pago` (VARCHAR)
- `cuenta_domicilio` (VARCHAR)
- `pagado` (BOOLEAN)
- `pago_id` (VARCHAR)

**Campos Calculados:**
- `el_arrendador` (VARCHAR)
- `el_arrendatario` (VARCHAR)
- `el_fiador` (VARCHAR)
- `el_fiador1` (VARCHAR)
- `nombre_fiador1` (VARCHAR)
- `inmueble_objeto` (VARCHAR)
- `precio_en_letra` (VARCHAR)
- `cuenta_formateada` (VARCHAR)
- `codigo_estado_texto` (VARCHAR)
- `clausula_fiador` (TEXT)

---

## 7. Próximos Pasos

1. **Ejecutar SQL de Campos Calculados**
   ```bash
   cd C:\Users\ferch\Desktop\Badillo\Arrendamientos-badillo
   psql -U postgres -d badilloDB -f database/add_campos_calculados.sql
   ```

2. **Verificar Datos Existentes**
   - Los contratos creados antes de estos cambios tendrán los campos calculados en NULL
   - Puede ser necesario regenerar estos campos para contratos antiguos

3. **Probar el Admin Panel**
   - Acceder a `/admin/contratos`
   - Verificar que los filtros funcionen
   - Revisar la vista de detalle de un contrato
   - Confirmar que los campos calculados se muestren correctamente

---

## 8. Notas Técnicas

### Búsqueda Mejorada
La búsqueda ahora incluye:
- Token del contrato
- Nombre del arrendatario
- Nombre del arrendador
- CURP del arrendatario
- CURP del arrendador
- Ciudad
- Colonia

### Paginación
Se mantiene la paginación de 20 registros por página.

### Ordenamiento
Los contratos se ordenan por fecha de creación descendente (más recientes primero).

### Badges de Estado
- **Verde (Pagado)**: Cuando `pagado = true`
- **Amarillo (Pendiente)**: Cuando `pagado = false`

---

## 9. Validación de Cambios

Para verificar que todo funciona correctamente:

```sql
-- Verificar estructura de la tabla
SELECT column_name, data_type, is_nullable
FROM information_schema.columns
WHERE table_name = 'contratos'
ORDER BY ordinal_position;

-- Verificar campos calculados
SELECT token, 
       el_arrendador, 
       el_arrendatario, 
       inmueble_objeto, 
       precio_en_letra
FROM contratos
LIMIT 5;
```

---

**Documento creado**: 19 de enero de 2025  
**Versión**: 1.0  
**Estado**: Completado - Pendiente ejecución SQL
