# API de Generación de Contratos de Arrendamiento

API RESTful para generar contratos de arrendamiento con integración de pagos mediante Clip.

## Base URL

```
https://tu-dominio.com/api
```

## Autenticación

No requiere autenticación para endpoints públicos.

---

## Endpoints

### 1. Crear Contrato y Procesar Pago

Procesa el formulario de contrato y genera un enlace de pago con Clip.

**Endpoint:** `POST /contrato`

**Headers:**
```json
{
  "Content-Type": "application/json",
  "Accept": "application/json"
}
```

**Body (JSON):**
```json
{
  "email": "cliente@ejemplo.com",
  "email_confirmation": "cliente@ejemplo.com",
  "nombre_arrendador": "Juan Pérez García",
  "curp_arrendador": "PEGJ850315HDFRRN09",
  "nombre_arrendatario": "María López Sánchez",
  "curp_arrendatario": "LOSM900520MDFRNN08",
  "tiene_fiador": false,
  "nombre_fiador": null,
  "curp_fiador": null,
  "tipo_inmueble": "DEPARTAMENTO",
  "uso_inmueble": "HABITACIONAL",
  "ubicacion": "Av. Reforma 123, Col. Centro, CP 06000",
  "estado": "CIUDAD DE MEXICO",
  "fecha_inicio": "2025-11-01",
  "plazo_meses": 12,
  "pago": 8500.00,
  "forma_pago": "TRANSFERENCIA"
}
```

**Validaciones:**

| Campo | Tipo | Requerido | Validación |
|-------|------|-----------|------------|
| `email` | string | Sí | Email válido |
| `email_confirmation` | string | Sí | Debe coincidir con `email` |
| `nombre_arrendador` | string | Sí | Máximo 255 caracteres |
| `curp_arrendador` | string | Sí | 18 caracteres, formato CURP válido |
| `nombre_arrendatario` | string | Sí | Máximo 255 caracteres |
| `curp_arrendatario` | string | Sí | 18 caracteres, formato CURP válido |
| `tiene_fiador` | boolean | No | Default: false |
| `nombre_fiador` | string | Condicional | Requerido si `tiene_fiador` = true |
| `curp_fiador` | string | Condicional | 18 caracteres si se proporciona |
| `tipo_inmueble` | string | Sí | `CASA`, `DEPARTAMENTO`, `LOCAL COMERCIAL`, `OFICINA`, `BODEGA`, `TERRENO` |
| `uso_inmueble` | string | Sí | `HABITACIONAL`, `COMERCIAL`, `INDUSTRIAL`, `MIXTO` |
| `ubicacion` | string | Sí | Dirección completa |
| `estado` | string | Sí | Nombre del estado mexicano (ver lista abajo) |
| `fecha_inicio` | date | Sí | Formato: YYYY-MM-DD |
| `plazo_meses` | integer | Sí | Mínimo: 1, Máximo: 48 |
| `pago` | numeric | Sí | Mínimo: 1 (MXN) |
| `forma_pago` | string | Sí | `EFECTIVO`, `TRANSFERENCIA`, `DEPOSITO`, `CHEQUE` |

**Estados válidos:**
```
AGUASCALIENTES, BAJA CALIFORNIA, BAJA CALIFORNIA SUR, CAMPECHE, CHIAPAS, 
CHIHUAHUA, CIUDAD DE MEXICO, COAHUILA, COLIMA, DURANGO, GUANAJUATO, 
GUERRERO, HIDALGO, JALISCO, MEXICO, MICHOACAN, MORELOS, NAYARIT, 
NUEVO LEON, OAXACA, PUEBLA, QUERETARO, QUINTANA ROO, SAN LUIS POTOSI, 
SINALOA, SONORA, TABASCO, TAMAULIPAS, TLAXCALA, VERACRUZ, YUCATAN, ZACATECAS
```

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "contrato_id": 1,
  "pago_id": 1,
  "token": "MARLONNAC0001",
  "amount": 8500.00,
  "redirect_payment": "https://tu-dominio.com/clip/pago/1"
}
```

**Errores de Validación (422):**
```json
{
  "success": false,
  "errors": {
    "curp_arrendador": [
      "El CURP del arrendador no tiene un formato válido"
    ],
    "email_confirmation": [
      "El correo de confirmación debe coincidir"
    ]
  }
}
```

**Error del Servidor (500):**
```json
{
  "success": false,
  "message": "Error al procesar el formulario: [detalle del error]"
}
```

---

### 2. Webhook de Clip (Interno)

Recibe notificaciones de pago desde Clip.

**Endpoint:** `POST /webhook/clip`

⚠️ **Uso interno**: Este endpoint es llamado automáticamente por Clip cuando se procesa un pago.

---

### 3. Consultar Estado de Pago

Consulta el estado actual de un pago en Clip.

**Endpoint:** `GET /clip/estado/{pago_id}`

**Parámetros:**
- `pago_id`: ID del pago generado

**Respuesta (200):**
```json
{
  "success": true,
  "status": "pending",
  "data": {
    "id": "clip_checkout_id",
    "status": "PENDING",
    "amount": 8500.00,
    "currency": "MXN"
  }
}
```

---

### 4. Descargar PDF (Web)

Descarga el PDF del contrato después del pago.

**Endpoint:** `GET /contrato/descargar/{token}`

**Parámetros:**
- `token`: Token generado (ej: `MARLONNAC0001`)

**Respuesta:**
- `200`: Descarga del archivo PDF
- `403`: Contrato no pagado
- `404`: PDF no encontrado

---

## Formato de Token

El token se genera automáticamente basándose en:
- **3 primeras letras** del primer nombre del arrendatario
- **3 últimas letras** del apellido paterno del arrendatario
- **Clave del estado** (2-4 caracteres)
- **Folio consecutivo** (4 dígitos)

**Ejemplo:**
```
Nombre: María López Sánchez
Estado: NUEVO LEON
Folio: 0001

Token: MARPEZNL0001
```

---

## Formato CURP

El CURP debe seguir el formato oficial mexicano:

```
[4 letras][6 dígitos][H/M][5 letras][1 alfanumérico][1 dígito]
```

**Ejemplo válido:** `HEGG560427MVZRRL04`

- **Posiciones 1-4**: Apellido paterno (1 letra) + Apellido materno (1 letra) + Nombre (2 letras)
- **Posiciones 5-10**: Fecha de nacimiento (AAMMDD)
- **Posición 11**: Sexo (H=Hombre, M=Mujer)
- **Posiciones 12-13**: Estado de nacimiento
- **Posiciones 14-16**: Consonantes internas
- **Posición 17**: Homoclave
- **Posición 18**: Dígito verificador

---

## Flujo de Integración

### Opción 1: Frontend Embebido

Si alojas el frontend en tu servidor:

```html
<!-- Iframe del formulario -->
<iframe 
  src="https://tu-dominio.com/contrato" 
  width="100%" 
  height="900px"
  frameborder="0">
</iframe>
```

### Opción 2: API Consumo Directo

Si construyes tu propio frontend:

```javascript
// Ejemplo con JavaScript/Fetch
const formData = {
  email: "cliente@ejemplo.com",
  email_confirmation: "cliente@ejemplo.com",
  nombre_arrendador: "Juan Pérez García",
  curp_arrendador: "PEGJ850315HDFRRN09",
  nombre_arrendatario: "María López Sánchez",
  curp_arrendatario: "LOSM900520MDFRNN08",
  tipo_inmueble: "CASA",
  uso_inmueble: "HABITACIONAL",
  ubicacion: "Calle 123",
  estado: "JALISCO",
  fecha_inicio: "2025-11-01",
  plazo_meses: 12,
  pago: 5000.00,
  forma_pago: "TRANSFERENCIA"
};

fetch('https://tu-dominio.com/api/contrato', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify(formData)
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    // Redirigir al usuario al pago
    window.location.href = data.redirect_payment;
  } else {
    console.error('Errores:', data.errors);
  }
})
.catch(error => console.error('Error:', error));
```

---

## Códigos de Respuesta HTTP

| Código | Significado |
|--------|-------------|
| 200 | Éxito |
| 422 | Error de validación |
| 403 | Prohibido (ej: contrato no pagado) |
| 404 | No encontrado |
| 500 | Error del servidor |

---

## Entornos

### Desarrollo/Sandbox
- API Base: `http://localhost:8000/api`
- Clip API: `https://api-gw-sandbox.payclip.com`

### Producción
- API Base: `https://tu-dominio.com/api`
- Clip API: `https://api-gw.payclip.com`

---

## Seguridad

- ✅ Validación CSRF en rutas web
- ✅ Validación de datos en backend
- ✅ CORS configurado para dominios permitidos
- ✅ Sanitización de inputs
- ✅ Webhook de Clip verificado

---

## Soporte

Para dudas técnicas o reportar problemas:
- Email: soporte@tu-dominio.com
- Documentación Clip: https://docs.payclip.com

---

## Changelog

### v1.0.0 (2025-10-31)
- ✨ Endpoint inicial de creación de contratos
- ✨ Integración con Clip Payment Gateway
- ✨ Generación de tokens personalizados
- ✨ Validación de formato CURP
- ✨ Webhook para procesamiento de pagos
