# Cambios en el Sistema de Pagos - InmoLegal

**Fecha:** 6 de noviembre de 2025  
**Versión:** 2.0

## Resumen de Cambios

Se implementó un nuevo sistema de pagos con las siguientes características:

### 1. **Precio Fijo: $19 MXN**
- ✅ Todos los contratos ahora tienen un precio fijo de **$19.00 MXN**
- ✅ El campo `pago` del formulario ya no determina el monto
- ✅ El precio se calcula automáticamente en `ContratoController::procesarFormulario()`

### 2. **Flujo para Pagos por TRANSFERENCIA**

**Proceso:**
1. Usuario llena el formulario y selecciona "TRANSFERENCIA"
2. Se crea el contrato en BD (sin PDF, `pagado = false`)
3. Se crea registro de pago pendiente
4. Se redirige a Clip para pago con tarjeta/transferencia
5. Usuario completa el pago en Clip
6. **Webhook de Clip confirma el pago**
7. **Se generan los PDFs en memoria:**
   - PDF del Contrato de Arrendamiento
   - PDF del Recibo de Pago
8. **Se envían los PDFs por correo electrónico** (adjuntos)
9. **Los PDFs NO se guardan en el servidor** (solo existen en memoria)
10. Se marca `contrato.pagado = true`

**Archivos modificados:**
- `app/Http/Controllers/ClipWebhookController.php` - Método `generarPDFsYEnviarCorreo()`
- `app/Mail/ContratoGenerado.php` - Email con PDFs adjuntos
- `resources/views/emails/contrato-generado.blade.php` - Vista del email

### 3. **Flujo para Pagos en EFECTIVO**

**Proceso:**
1. Usuario llena el formulario y selecciona "EFECTIVO"
2. Se crea el contrato en BD (sin PDF, `pagado = false`)
3. Se crea registro de pago pendiente con `payment_method = 'EFECTIVO'`
4. Se redirige a Clip para generar enlace de pago en efectivo (OXXO, 7-Eleven, etc.)
5. **Se envía correo con TOKEN (sin PDFs)**
   - Incluye token del contrato
   - Incluye enlace para completar pago
   - **NO incluye PDFs** (aún no se generan)
6. Usuario recibe referencia de pago en efectivo de Clip
7. Usuario realiza el pago en tienda física
8. **Webhook de Clip confirma el pago**
9. **Se generan los PDFs en memoria:**
   - PDF del Contrato de Arrendamiento
   - PDF del Recibo de Pago
10. **Se envían los PDFs por correo electrónico** (adjuntos)
11. **Los PDFs NO se guardan en el servidor** (solo existen en memoria)
12. Se marca `contrato.pagado = true`

**Archivos nuevos:**
- `app/Mail/PagoEfectivoPendiente.php` - Email para pago en efectivo pendiente
- `resources/views/emails/pago-efectivo-pendiente.blade.php` - Vista del email

**Archivos modificados:**
- `app/Http/Controllers/ClipPaymentController.php` - Método `iniciarPago()` detecta tipo de pago

### 4. **Webhooks de Clip**

El webhook (`/webhook/clip`) maneja los siguientes eventos:
- `payment.paid` / `charge.paid` / `checkout.paid` → Genera PDFs y envía correo
- `payment.failed` / `charge.failed` / `checkout.failed` → Marca pago como fallido
- `payment.refunded` / `charge.refunded` → Marca pago como reembolsado

**IMPORTANTE:** Los PDFs **SOLO** se generan cuando el webhook confirma el pago exitoso, independientemente del método de pago (TRANSFERENCIA o EFECTIVO).

### 5. **Generación de PDFs**

**Estrategia: Generación bajo demanda**
- ✅ Los PDFs se generan **en memoria** cuando se necesitan
- ✅ **NO se guardan** en el servidor (sin almacenamiento)
- ✅ Se generan en estos momentos:
  - Al enviar email después del webhook (adjuntos)
  - Al descargar mediante el token
- ✅ Ventajas:
  - No consume espacio en disco
  - Siempre se genera con datos actualizados
  - Más seguro (no hay archivos sensibles almacenados)

**Ubicaciones donde se generan:**
- `ClipWebhookController::generarPDFsYEnviarCorreo()` - Para envío por email
- `ContratoController::descargar()` - Para descarga directa

### 6. **Archivos Modificados**

```
app/
  ├── Http/Controllers/
  │   ├── ContratoController.php ............... Precio fijo $19, descarga bajo demanda
  │   ├── ClipPaymentController.php ............ Detecta tipo de pago, envía email si EFECTIVO
  │   └── ClipWebhookController.php ............ Genera PDFs en memoria al confirmar pago
  └── Mail/
      ├── ContratoGenerado.php ................. Email con PDFs (TRANSFERENCIA o EFECTIVO confirmado)
      └── PagoEfectivoPendiente.php ............ Email con TOKEN (solo EFECTIVO pendiente) [NUEVO]

resources/views/
  ├── contratos/
  │   └── success.blade.php .................... Muestra descarga solo si pagado=true
  └── emails/
      ├── contrato-generado.blade.php .......... Vista email con PDFs
      └── pago-efectivo-pendiente.blade.php .... Vista email con TOKEN [NUEVO]
```

## Testing

### Probar Pago por TRANSFERENCIA
1. Ir a: https://oceanairti.sytes.net/inmolegal/contrato
2. Llenar formulario, seleccionar "TRANSFERENCIA"
3. Completar pago en Clip (tarjeta de prueba)
4. Verificar que llega email con PDFs adjuntos

### Probar Pago en EFECTIVO
1. Ir a: https://oceanairti.sytes.net/inmolegal/contrato
2. Llenar formulario, seleccionar "EFECTIVO"
3. Verificar que llega email con TOKEN (sin PDFs)
4. Generar referencia de pago en Clip
5. Simular pago (o webhook manual)
6. Verificar que llega SEGUNDO email con PDFs adjuntos

## Notas Importantes

⚠️ **Los PDFs se generan SOLO en memoria** (no se guardan en el servidor)  
⚠️ **Los PDFs se generan cuando:**
- Webhook de Clip confirma el pago (para envío por email)
- Usuario descarga usando el token (generación bajo demanda)  
⚠️ **Para EFECTIVO:** Se envían 2 correos (primero token, luego PDFs)  
⚠️ **Para TRANSFERENCIA:** Se envía 1 correo (con PDFs después del webhook)  
⚠️ **Webhook debe estar configurado en Clip:** https://oceanairti.sytes.net/inmolegal/webhook/clip

## Pendientes

- [ ] Configurar webhook en panel de Clip
- [ ] Probar flujo completo de pago en efectivo con Clip sandbox
- [ ] Verificar formato de evento del webhook de Clip para pagos en efectivo
- [ ] Implementar reintento automático de generación de PDF si falla el envío de email

## Próximos Pasos

1. Probar en ambiente de pruebas (sandbox de Clip)
2. Verificar correos electrónicos en ambos flujos
3. Validar generación correcta de PDFs
4. Configurar webhook en panel de Clip
5. Realizar pruebas de extremo a extremo

---

**Desarrollado por:** GitHub Copilot  
**Revisión:** Pendiente
