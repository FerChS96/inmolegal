# Configuración del Webhook de Clip

## ✅ Webhook Implementado

El webhook de Clip ya está implementado y funcionando localmente en:
- **Endpoint**: `POST /webhook/clip`
- **Test Endpoint**: `GET|POST /webhook/clip/test`

### Eventos Soportados:
1. ✅ `payment.paid` / `charge.paid` / `checkout.paid` - Pago exitoso
2. ✅ `payment.failed` / `charge.failed` - Pago fallido
3. ✅ `payment.refunded` / `charge.refunded` - Reembolso

### Funcionalidad:
- ✅ Registra todos los webhooks en logs de Laravel
- ✅ Actualiza automáticamente el estado del pago en tabla `pagos`
- ✅ Actualiza el contrato con `fecha_pago`, `monto_pagado`, `metodo_pago`
- ✅ Envía emails con PDFs (cuando se configure SMTP)
- ✅ Previene duplicados con verificación de `webhook_attempts`
- ✅ Sin protección CSRF para recibir peticiones externas

---

## 🌐 Para Exponer el Webhook (Desarrollo)

Como estás en desarrollo local (`localhost:8001`), Clip no puede llamar directamente a tu webhook. Necesitas **exponer tu servidor local a internet**.

### Opción 1: ngrok (Recomendado para testing)

1. **Descargar ngrok**:
   ```powershell
   # Descarga desde: https://ngrok.com/download
   # O con Chocolatey:
   choco install ngrok
   ```

2. **Exponer puerto 8001**:
   ```powershell
   ngrok http 8001
   ```

3. **Copiar URL pública** (ejemplo: `https://abc123.ngrok.io`)

4. **Registrar webhook en Clip**:
   - URL del webhook: `https://abc123.ngrok.io/webhook/clip`
   - Esto se configura automáticamente cuando creas el checkout (ya lo agregamos en `ClipPaymentController`)

### Opción 2: Cloudflare Tunnel (Alternativa gratuita)

1. **Instalar Cloudflare Tunnel**:
   ```powershell
   # Descarga desde: https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/downloads/
   ```

2. **Crear túnel**:
   ```powershell
   cloudflared tunnel --url http://localhost:8001
   ```

3. **Usar la URL generada** para el webhook

---

## 📝 Configuración en Clip

El webhook ya está configurado en el código:

```php
// En ClipPaymentController.php línea ~89
'webhook_url' => route('webhook.clip'),
```

Cuando creas un checkout, Clip automáticamente:
1. Registra tu `webhook_url`
2. Envía notificaciones POST cuando el pago cambia de estado
3. Incluye datos del pago en formato JSON

---

## 🧪 Testing Local

### 1. Probar que el endpoint funciona:
```powershell
curl http://localhost:8001/webhook/clip/test
```

### 2. Simular webhook de pago exitoso:
```powershell
$body = @{
    type = "payment.paid"
    data = @{
        id = "test_payment_123"
        amount = 2000.00
        payment_method = "card"
        status = "paid"
    }
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8001/webhook/clip" -Method Post -Body $body -ContentType "application/json"
```

### 3. Ver logs en tiempo real:
```powershell
Get-Content "storage\logs\laravel.log" -Wait -Tail 50
```

---

## 🔍 Verificar Webhooks en Producción

Cuando tengas un servidor público, puedes:

1. **Ver logs de webhooks recibidos**:
   - Panel Admin → Ver en logs de Laravel
   - Tabla `pagos` → campo `webhook_data`

2. **Consultar estado en Clip**:
   ```
   GET /clip/estado/{pago}
   ```

3. **Reenviar webhook manualmente** (si falla):
   - Desde el dashboard de Clip
   - O crear script de reintento

---

## ⚙️ Variables de Entorno

Ya configuradas en `.env`:
```env
CLIP_API_KEY=test_8d53cc9d-1f3e-4f0f-8f5c-c5cc9583879b
CLIP_SECRET_KEY=07f60ed2-c080-470d-ab5d-99f7b2cdeda8
CLIP_API_URL=https://api.payclip.com
CLIP_ENVIRONMENT=test
```

Para producción, cambiar a credenciales reales y `CLIP_ENVIRONMENT=production`

---

## 🚀 Próximos Pasos

1. ✅ **Webhook implementado** - Listo para recibir notificaciones
2. ⏳ **Exponer servidor** - Usar ngrok o Cloudflare Tunnel
3. ⏳ **Probar flujo completo**:
   - Llenar formulario
   - Pagar con tarjeta de prueba
   - Verificar que webhook actualiza automáticamente
4. ⏳ **Configurar SMTP** - Para enviar emails automáticos
5. ⏳ **Deploy en producción** - Con dominio público

---

## 📊 Flujo Completo con Webhook

```
Usuario → Formulario → Crear Contrato + Pago (status=pending)
    ↓
Clip Checkout (con webhook_url registrada)
    ↓
Usuario paga con tarjeta
    ↓
Clip procesa pago
    ↓
Clip envía POST a tu webhook → {type: "payment.paid", data: {...}}
    ↓
Tu webhook:
  - Actualiza pago: status=paid, paid_at=now()
  - Actualiza contrato: fecha_pago, monto_pagado
  - Envía email con PDFs
    ↓
Usuario ve estado actualizado en admin panel
```

---

## 🐛 Troubleshooting

**Problema**: Webhook no recibe notificaciones
- ✅ Verificar que ngrok/cloudflare esté corriendo
- ✅ Verificar URL pública en logs de Clip
- ✅ Revisar `storage/logs/laravel.log`

**Problema**: Error 419 CSRF Token Mismatch
- ✅ Ya está excluido en `VerifyCsrfToken.php`

**Problema**: Pago no se actualiza después de webhook
- ✅ Revisar logs para ver errores
- ✅ Verificar que `payment_request_id` coincida

---

## 📞 Estado Actual

✅ Webhook implementado y testeado localmente
✅ Rutas configuradas y sin CSRF
✅ Logs funcionando correctamente
⏳ Pendiente: Exponer servidor con ngrok
⏳ Pendiente: Probar con pago real de Clip
