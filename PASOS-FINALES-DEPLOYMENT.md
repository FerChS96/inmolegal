# ✅ Instalación Completada - Pasos Finales

## 📋 Estado Actual

✅ **Composer instalado correctamente**
- Laravel Framework 10.49.1
- PHP 8.1.31
- Todas las dependencias instaladas
- Autoloader optimizado

✅ **Caches de Laravel optimizados**
- Config cache generado
- Route cache generado
- View cache generado

✅ **Permisos configurados**
- `storage/` - Permisos completos para IIS_IUSRS
- `bootstrap/cache/` - Permisos completos para IIS_IUSRS

✅ **Storage link creado**
- Enlace simbólico público/storage → storage/app/public

---

## 🔴 PASO FINAL REQUERIDO (Como Administrador)

**Debes ejecutar este comando en PowerShell COMO ADMINISTRADOR:**

```powershell
iisreset
```

O reiniciar el Application Pool específico:

```powershell
Import-Module WebAdministration
Restart-WebAppPool -Name "DefaultAppPool"
# O si creaste un pool específico:
# Restart-WebAppPool -Name "InmoLegalPool"
```

---

## 🧪 Testing Después del Reinicio

### 1. Verificar que el sitio carga

```powershell
# Test básico del dominio
Invoke-WebRequest -Uri "https://oceanairti.sytes.net/inmolegal" -UseBasicParsing

# O desde el navegador:
# https://oceanairti.sytes.net/inmolegal
```

### 2. Verificar rutas principales

Abre en el navegador:

- ✅ **Home**: `https://oceanairti.sytes.net/inmolegal`
- ✅ **Formulario**: `https://oceanairti.sytes.net/inmolegal/contrato`
- ✅ **Widget JS**: `https://oceanairti.sytes.net/inmolegal/inmolegal-widget.js`
- ✅ **Admin**: `https://oceanairti.sytes.net/inmolegal/admin/login`
- ✅ **Webhook**: `https://oceanairti.sytes.net/inmolegal/webhook/clip`

### 3. Verificar archivos estáticos (CSS/JS)

Abre las herramientas de desarrollo del navegador (F12) y verifica que:
- CSS se carga correctamente
- JavaScript se carga correctamente
- No hay errores 404 en la consola

---

## 🐛 Si hay errores después del reinicio

### Error 500 - Internal Server Error

1. **Verificar logs de Laravel**:
   ```powershell
   Get-Content "C:\inetpub\wwwroot\inmolegal\storage\logs\laravel.log" -Tail 50
   ```

2. **Habilitar debug temporalmente** (en `.env`):
   ```env
   APP_DEBUG=true
   ```
   
3. **Limpiar caches nuevamente**:
   ```powershell
   cd C:\inetpub\wwwroot\inmolegal
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan cache:clear
   ```

### Error 404 - Rutas no funcionan

1. **Verificar web.config** en `public/`:
   ```powershell
   Test-Path "C:\inetpub\wwwroot\inmolegal\public\web.config"
   # Debe retornar: True
   ```

2. **Verificar URL Rewrite Module**:
   - Abrir IIS Manager
   - Seleccionar el sitio
   - Buscar icono "URL Rewrite"
   - Si no existe, instalar desde: https://www.iis.net/downloads/microsoft/url-rewrite

3. **Verificar que IIS apunta a `public/`**:
   - En IIS Manager
   - Seleccionar el sitio/aplicación "inmolegal"
   - Click derecho → Manage Application → Advanced Settings
   - Verificar Physical Path: `C:\inetpub\wwwroot\inmolegal\public`

### CSS/JS no cargan (Error 404)

1. **Verificar APP_URL** en `.env`:
   ```env
   APP_URL=https://oceanairti.sytes.net/inmolegal
   ```

2. **Regenerar assets** (si usas Vite/npm):
   ```powershell
   npm run build
   ```

3. **Limpiar cache del navegador** (Ctrl+Shift+Delete)

### Base de datos no conecta

1. **Verificar PostgreSQL está corriendo**:
   ```powershell
   Get-Service -Name "postgresql*"
   ```

2. **Verificar credenciales** en `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=badilloDB
   DB_USERNAME=postgres
   DB_PASSWORD=tu_password_aqui
   ```

3. **Test de conexión**:
   ```powershell
   php artisan tinker
   # Luego ejecutar:
   # DB::connection()->getPdo();
   ```

---

## 📊 Verificar Estado del Sitio

### Comando de prueba completo

```powershell
# Test general
$uri = "https://oceanairti.sytes.net/inmolegal"
try {
    $response = Invoke-WebRequest -Uri $uri -UseBasicParsing
    Write-Host "✅ Sitio funcionando - Status: $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Test del formulario
$formUri = "https://oceanairti.sytes.net/inmolegal/contrato"
try {
    $response = Invoke-WebRequest -Uri $formUri -UseBasicParsing
    Write-Host "✅ Formulario funcionando - Status: $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "❌ Error en formulario: $($_.Exception.Message)" -ForegroundColor Red
}

# Test del widget
$widgetUri = "https://oceanairti.sytes.net/inmolegal/inmolegal-widget.js"
try {
    $response = Invoke-WebRequest -Uri $widgetUri -UseBasicParsing
    Write-Host "✅ Widget funcionando - Status: $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "❌ Error en widget: $($_.Exception.Message)" -ForegroundColor Red
}
```

---

## 📝 Verificar Configuración de IIS

### Checklist de IIS Manager

1. **Abrir IIS Manager** (inetmgr)

2. **Verificar Sitio/Aplicación**:
   - [ ] Existe entrada "inmolegal" bajo Sites o como aplicación
   - [ ] Physical Path apunta a: `C:\inetpub\wwwroot\inmolegal\public`
   - [ ] Application Pool asignado

3. **Verificar Application Pool**:
   - [ ] .NET CLR Version: **No Managed Code**
   - [ ] Managed Pipeline Mode: **Integrated**
   - [ ] Identity: ApplicationPoolIdentity o IIS_IUSRS

4. **Verificar Handler Mappings**:
   - [ ] Existe handler "PHP_via_FastCGI"
   - [ ] Path: `*.php`
   - [ ] Executable: Ruta correcta a `php-cgi.exe`

5. **Verificar URL Rewrite**:
   - [ ] URL Rewrite Module instalado
   - [ ] Regla "Laravel" existe y está habilitada

---

## 🔄 Actualizar Webhook de Clip

Si estás en producción, actualiza las URLs en el panel de Clip:

- **Webhook URL**: `https://oceanairti.sytes.net/inmolegal/webhook/clip`
- **Return URL**: `https://oceanairti.sytes.net/inmolegal/contrato/confirmacion`
- **Cancel URL**: `https://oceanairti.sytes.net/inmolegal/contrato/cancelado`

---

## 📧 Verificar Configuración de Email

Test de envío de email:

```powershell
php artisan tinker

# Dentro de tinker:
Mail::raw('Test email', function ($message) {
    $message->to('tu-email@example.com')
            ->subject('Test InmoLegal');
});
```

---

## 🔐 Seguridad - Recordatorio

### Antes de ir a producción completa:

1. **Cambiar APP_DEBUG** en `.env`:
   ```env
   APP_DEBUG=false
   ```

2. **Cambiar httpErrors** en `web.config`:
   ```xml
   <httpErrors errorMode="Custom" existingResponse="Replace" />
   ```

3. **Configurar SSL/HTTPS** (Let's Encrypt o certificado comercial)

4. **Cambiar contraseñas por defecto**:
   - Password de admin
   - Credenciales de base de datos
   - Keys de Clip (producción)

5. **Verificar .env** no sea accesible públicamente:
   ```powershell
   # Esto debe dar error 404:
   Invoke-WebRequest -Uri "https://oceanairti.sytes.net/inmolegal/.env" -UseBasicParsing
   ```

---

## ✅ Checklist Final de Deployment

- [ ] Composer install completado ✅
- [ ] Caches de Laravel generados ✅
- [ ] Permisos de storage configurados ✅
- [ ] Permisos de bootstrap/cache configurados ✅
- [ ] Storage link creado ✅
- [ ] **IIS reiniciado (PENDIENTE - requiere admin)**
- [ ] Sitio carga correctamente en navegador
- [ ] Rutas funcionan (home, formulario, admin, etc.)
- [ ] CSS y JS cargan correctamente
- [ ] Base de datos conecta
- [ ] Emails se envían correctamente
- [ ] Webhook de Clip configurado
- [ ] APP_DEBUG=false en producción
- [ ] SSL/HTTPS configurado
- [ ] Contraseñas cambiadas

---

## 📞 URLs de Prueba

Una vez reiniciado IIS, probar estas URLs:

```
✅ Home:      https://oceanairti.sytes.net/inmolegal
✅ Contrato:  https://oceanairti.sytes.net/inmolegal/contrato
✅ Widget:    https://oceanairti.sytes.net/inmolegal/inmolegal-widget.js
✅ Admin:     https://oceanairti.sytes.net/inmolegal/admin/login
✅ Dashboard: https://oceanairti.sytes.net/inmolegal/admin
```

---

## 🆘 Soporte

Si encuentras problemas:

1. **Revisar logs**:
   ```powershell
   # Laravel
   Get-Content "C:\inetpub\wwwroot\inmolegal\storage\logs\laravel.log" -Tail 100
   
   # IIS (ajustar fecha)
   Get-Content "C:\inetpub\logs\LogFiles\W3SVC1\u_ex$(Get-Date -Format 'yyMMdd').log" -Tail 50
   ```

2. **Verificar configuración**:
   ```powershell
   php artisan about
   ```

3. **Test de base de datos**:
   ```powershell
   php artisan migrate:status
   ```

---

**Fecha de instalación**: 4 de Noviembre, 2025
**Versión Laravel**: 10.49.1
**Versión PHP**: 8.1.31
**Servidor**: IIS 10.0
**Base de datos**: PostgreSQL

---

## ✨ ¡Todo listo!

Una vez que ejecutes `iisreset` como administrador, tu aplicación InmoLegal estará completamente configurada y lista para funcionar en:

🌐 **https://oceanairti.sytes.net/inmolegal**

---
