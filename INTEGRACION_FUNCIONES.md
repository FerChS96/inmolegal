# Integración de Funciones de server.js a Laravel

## Fecha: 6 de noviembre de 2025

## Resumen de Cambios

Se han integrado exitosamente las funciones del archivo `server.js` (Node.js/Firebase) al sistema Laravel. Estas funciones permiten calcular automáticamente campos del contrato basados en datos como CURP, tipo de inmueble, y forma de pago.

---

## Archivos Creados

### 1. `app/Helpers/ContratoHelper.php`
Helper class con las siguientes funciones estáticas:

#### Funciones Principales:

- **`obtenerGeneroYArticulo($curp, $masculino, $femenino)`**
  - Analiza el carácter 11 del CURP para determinar género
  - Retorna: "EL ARRENDADOR" o "LA ARRENDADORA"
  - Ejemplo: `XEXX010101HDFXXX00` → "EL ARRENDADOR"

- **`determinarGenero($curp)`**
  - Retorna solo el artículo: "EL" o "LA"
  - Útil para construcciones gramaticales simples

- **`obtenerInmuebleConArticulo($inmueble)`**
  - Determina artículo según género del sustantivo
  - Femeninos: casa, oficina, bodega
  - Ejemplo: "casa" → "una casa ubicada"
  - Ejemplo: "departamento" → "un departamento ubicado"

- **`numeroALetras($numero)`**
  - Convierte números a texto en español
  - Soporta hasta miles
  - Ejemplo: 3275 → "TRES MIL DOSCIENTOS SETENTA Y CINCO"

- **`obtenerCodigoEstado($estado)`**
  - Formatea nombre del estado
  - Ejemplo: "Ciudad de México" → "la Ciudad de México"
  - Ejemplo: "Jalisco" → "el Estado de Jalisco"

- **`obtenerCuentaFormateada($formaPago, $cuenta)`**
  - Si es transferencia: "la Cuenta CLABE 012345..."
  - Si no: retorna cuenta/domicilio sin modificar

**Nota:** La función `generateRandomToken()` NO se incluyó porque el sistema Laravel ya tiene su propio método de generación de tokens en `ContratoController::generarTokenDesdeDatos()`.

---

## Archivos Modificados

### 2. `app/Http/Controllers/ContratoController.php`

**Cambios:**
- Se importó `use App\Helpers\ContratoHelper;`
- Se agregó lógica en el método `procesarFormulario()` para calcular campos automáticamente antes de guardar:

```php
// Calcular campos adicionales usando ContratoHelper
$elArrendador = ContratoHelper::obtenerGeneroYArticulo($request->curp_arrendador, 'ARRENDADOR', 'ARRENDADORA');
$elArrendatario = ContratoHelper::obtenerGeneroYArticulo($request->curp_arrendatario, 'ARRENDATARIO', 'ARRENDATARIA');
$inmuebleObjeto = ContratoHelper::obtenerInmuebleConArticulo($request->tipo_inmueble);
$precioEnLetra = ContratoHelper::numeroALetras($monto);
$codigoEstado = ContratoHelper::obtenerCodigoEstado($request->estado);
$cuentaFormateada = ContratoHelper::obtenerCuentaFormateada($request->forma_pago, $request->cuenta_domicilio ?? '');

// Calcular campos del fiador si existe
if ($request->boolean('tiene_fiador') && $request->curp_fiador) {
    $elFiador = ContratoHelper::obtenerGeneroYArticulo($request->curp_fiador, 'FIADOR', 'FIADORA');
    $elFiador1 = "y como {$elFiador} ";
    // ... más campos
}
```

**Campos guardados en la base de datos:**
- `el_arrendador`: "EL ARRENDADOR" / "LA ARRENDADORA"
- `el_arrendatario`: "EL ARRENDATARIO" / "LA ARRENDATARIA"
- `inmueble_objeto`: "una casa ubicada" / "un departamento ubicado"
- `precio_en_letra`: "TRES MIL DOSCIENTOS PESOS"
- `codigo_estado_texto`: "el Estado de Jalisco"
- `cuenta_formateada`: "la Cuenta CLABE ..."
- `el_fiador`: "EL FIADOR" / "LA FIADORA" (si aplica)
- `el_fiador1`: "y como EL FIADOR " (si aplica)
- `nombre_fiador1`: "JUAN PÉREZ GARCÍA con CURP " (si aplica)
- `clausula_fiador`: Texto completo de la cláusula del fiador (si aplica)

---

### 3. `app/Models/Contrato.php`

**Cambios:**
- Se agregaron los nuevos campos al array `$fillable`:
```php
// Campos calculados automáticamente
'el_arrendador',
'el_arrendatario',
'inmueble_objeto',
'precio_en_letra',
'codigo_estado_texto',
'cuenta_formateada',
'el_fiador',
'el_fiador1',
'nombre_fiador1',
'clausula_fiador',
```

---

### 4. Base de Datos

**Actualización en:** `esquemaSQL.txt`

**Columnas agregadas a la tabla `contratos`:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `el_arrendador` | VARCHAR(50) | "EL ARRENDADOR" / "LA ARRENDADORA" |
| `el_arrendatario` | VARCHAR(50) | "EL ARRENDATARIO" / "LA ARRENDATARIA" |
| `inmueble_objeto` | VARCHAR(100) | "una casa ubicada" / "un departamento ubicado" |
| `precio_en_letra` | VARCHAR(500) | Precio en palabras |
| `codigo_estado_texto` | VARCHAR(100) | "el Estado de Jalisco" |
| `cuenta_formateada` | VARCHAR(255) | Cuenta/domicilio formateado |
| `el_fiador` | VARCHAR(50) | "EL FIADOR" / "LA FIADORA" |
| `el_fiador1` | VARCHAR(100) | "y como EL FIADOR " |
| `nombre_fiador1` | VARCHAR(255) | Nombre completo + "con CURP " |
| `clausula_fiador` | TEXT | Texto completo de la cláusula |

**Estado:** ✅ Esquema SQL actualizado

**Nota importante:** Este proyecto NO usa migraciones de Laravel. Los cambios en la base de datos se aplican directamente ejecutando el script SQL en PostgreSQL.

---

## Pruebas Realizadas

Se creó el archivo `test_helper.php` con 12 pruebas de las funciones:

✅ Todas las pruebas pasaron correctamente:
- Género y artículo (hombre/mujer)
- Inmueble con artículo (femenino/masculino)
- Número a letras (simple/complejo/decimales)
- Código de estado (CDMX/otros estados)
- Cuenta formateada (transferencia/efectivo)

---

## Beneficios de la Integración

1. **Consistencia de datos**: Los campos se calculan automáticamente, evitando errores manuales
2. **Formato correcto**: Género gramatical correcto según el CURP
3. **Legibilidad**: Precios en letra para documentos legales
4. **Almacenamiento**: Campos pre-calculados mejoran rendimiento en generación de PDFs
5. **Reutilización**: Helper puede usarse en otros controladores o vistas

---

## Uso en Generación de PDFs

Los campos calculados ahora están disponibles en el objeto `$contrato`:

```php
// En la vista Blade del PDF:
{{ $contrato->el_arrendador }} <!-- "EL ARRENDADOR" -->
{{ $contrato->inmueble_objeto }} <!-- "una casa ubicada" -->
{{ $contrato->precio_en_letra }} <!-- "TRES MIL PESOS" -->
{{ $contrato->clausula_fiador }} <!-- Cláusula completa del fiador -->
```

---

## Próximos Pasos Recomendados

1. ✅ **Completado**: Integrar funciones al sistema
2. ✅ **Completado**: Actualizar esquema SQL con campos calculados
3. ⏳ **Pendiente**: Ejecutar script SQL para agregar columnas en PostgreSQL
4. ⏳ **Pendiente**: Actualizar template PDF para usar campos calculados
5. ⏳ **Pendiente**: Actualizar panel administrativo para mostrar campos calculados
6. ⏳ **Pendiente**: Probar generación completa de contrato con datos reales

---

## Notas Técnicas

- Las funciones mantienen compatibilidad con el comportamiento del `server.js` original
- Se usa análisis del carácter 11 del CURP (H=Hombre, M=Mujer)
- La conversión de números a letras soporta hasta 999,999
- Los campos nullable permiten valores NULL si no aplican (ej: sin fiador)
- Las pruebas confirman comportamiento idéntico al sistema Node.js
- **NO se usan migraciones de Laravel** - Los cambios se aplican directamente en PostgreSQL
- El sistema ya tiene su propia lógica de generación de tokens en `ContratoController`

---

## Script SQL para Aplicar Cambios

Para agregar las nuevas columnas a la base de datos existente, ejecuta:

```sql
-- Agregar campos calculados a la tabla contratos
ALTER TABLE contratos ADD COLUMN el_arrendador VARCHAR(50) NULL;
ALTER TABLE contratos ADD COLUMN el_arrendatario VARCHAR(50) NULL;
ALTER TABLE contratos ADD COLUMN inmueble_objeto VARCHAR(100) NULL;
ALTER TABLE contratos ADD COLUMN precio_en_letra VARCHAR(500) NULL;
ALTER TABLE contratos ADD COLUMN codigo_estado_texto VARCHAR(100) NULL;
ALTER TABLE contratos ADD COLUMN cuenta_formateada VARCHAR(255) NULL;
ALTER TABLE contratos ADD COLUMN el_fiador VARCHAR(50) NULL;
ALTER TABLE contratos ADD COLUMN el_fiador1 VARCHAR(100) NULL;
ALTER TABLE contratos ADD COLUMN nombre_fiador1 VARCHAR(255) NULL;
ALTER TABLE contratos ADD COLUMN clausula_fiador TEXT NULL;

-- Agregar comentarios a las columnas para documentación
COMMENT ON COLUMN contratos.el_arrendador IS '"EL ARRENDADOR" / "LA ARRENDADORA" (según CURP)';
COMMENT ON COLUMN contratos.el_arrendatario IS '"EL ARRENDATARIO" / "LA ARRENDATARIA" (según CURP)';
COMMENT ON COLUMN contratos.inmueble_objeto IS '"una casa ubicada" / "un departamento ubicado"';
COMMENT ON COLUMN contratos.precio_en_letra IS 'Precio convertido a letras (ej: "TRES MIL PESOS")';
COMMENT ON COLUMN contratos.codigo_estado_texto IS '"el Estado de Jalisco" / "la Ciudad de México"';
COMMENT ON COLUMN contratos.cuenta_formateada IS '"la Cuenta CLABE ..." o domicilio formateado';
COMMENT ON COLUMN contratos.el_fiador IS '"EL FIADOR" / "LA FIADORA" (según CURP)';
COMMENT ON COLUMN contratos.el_fiador1 IS '"y como EL FIADOR " (para texto del contrato)';
COMMENT ON COLUMN contratos.nombre_fiador1 IS '"JUAN PÉREZ GARCÍA con CURP " (nombre completo + texto)';
COMMENT ON COLUMN contratos.clausula_fiador IS 'Cláusula completa del fiador (párrafo HTML)';
```

---

## Comandos Ejecutados

```bash
# Ejecutar pruebas del helper
php test_helper.php
```

---

## Autor
Integración realizada el 6 de noviembre de 2025
