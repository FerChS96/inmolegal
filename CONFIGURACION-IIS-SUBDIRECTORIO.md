# ✅ Configuración Revisada para IIS - Subdirectorio

## 📍 Configuración Actual
- **Dominio**: oceanairti.sytes.net/inmolegal
- **Ruta física**: `C:\inetpub\wwwroot\inmolegal\public`
- **Tipo**: Aplicación Laravel en subdirectorio

---

## ✨ Cambios Realizados en web.config

### 1. **Regla de URL Rewrite Mejorada**
```xml
<action type="Rewrite" url="index.php" appendQueryString="true" />
```
- ✅ Cambiado de `url="index.php/{R:1}"` a `url="index.php"`
- ✅ Agregado `appendQueryString="true"` para preservar parámetros
- ✅ Mejor manejo de rutas en subdirectorios

### 2. **Variables de Servidor Permitidas**
```xml
<allowedServerVariables>
    <add name="SCRIPT_NAME" />
    <add name="HTTP_X_ORIGINAL_URL" />
</allowedServerVariables>
```
- ✅ Permite a Laravel detectar correctamente el subdirectorio
- ✅ Preserva la URL original para mejor routing

### 3. **Condiciones de Rewrite Mejoradas**
```xml
<conditions logicalGrouping="MatchAll">
```
- ✅ Asegura que todas las condiciones se cumplan simultáneamente

---

## 🔧 Configuración Requerida en IIS Manager

### 1. Configuración del Sitio/Aplicación

**Opción A: Como Aplicación Dentro de un Sitio Existente** (RECOMENDADO)
```
1. Abrir IIS Manager
2. Expandir "Sites" → [Sitio Principal]
3. Click derecho en "inmolegal" → "Convert to Application"
4. Configurar:
   - Alias: inmolegal
   - Physical path: C:\inetpub\wwwroot\inmolegal\public
   - Application pool: (crear uno nuevo llamado "InmoLegalPool")
```

**Opción B: Como Directorio Virtual**
```
1. Abrir IIS Manager
2. Expandir "Sites" → [Sitio Principal]
3. Click derecho → Add Virtual Directory
4. Configurar:
   - Alias: inmolegal
   - Physical path: C:\inetpub\wwwroot\inmolegal\public
```

### 2. Configurar Application Pool (Si es Aplicación)

```
1. Click en "Application Pools"
2. Seleccionar "InmoLegalPool"
3. Configurar:
   - .NET CLR Version: No Managed Code
   - Managed Pipeline Mode: Integrated
   - Start Mode: AlwaysRunning
   - Identity: ApplicationPoolIdentity
```

### 3. Verificar URL Rewrite Module

```powershell
# Verificar si está instalado
Get-WindowsFeature -Name Web-Url-Rewrite

# Si no está instalado, descargarlo de:
# https://www.iis.net/downloads/microsoft/url-rewrite
```

---

## 🔐 Permisos de Carpetas

Ejecutar en PowerShell como Administrador:

```powershell
# Dar permisos a IIS_IUSRS
icacls "C:\inetpub\wwwroot\inmolegal\storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\inetpub\wwwroot\inmolegal\bootstrap\cache" /grant "IIS_IUSRS:(OI)(CI)F" /T

# Dar permisos al Application Pool Identity (si usas aplicación separada)
icacls "C:\inetpub\wwwroot\inmolegal\storage" /grant "IIS APPPOOL\InmoLegalPool:(OI)(CI)F" /T
icacls "C:\inetpub\wwwroot\inmolegal\bootstrap\cache" /grant "IIS APPPOOL\InmoLegalPool:(OI)(CI)F" /T
```

---

## ⚙️ Verificar Configuración de .env

Tu archivo `.env` debe tener:

```env
APP_URL=https://oceanairti.sytes.net/inmolegal

# Asegúrate de que esté configurado correctamente
APP_ENV=production
APP_DEBUG=false  # IMPORTANTE: false en producción
```

---

## 🧹 Limpiar Cache de Laravel

Después de cambios en web.config, ejecutar:

```powershell
cd C:\inetpub\wwwroot\inmolegal

# Limpiar todos los caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Regenerar caches para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔄 Reiniciar IIS

```powershell
# Reiniciar IIS completamente
iisreset

# O solo el Application Pool específico
Restart-WebAppPool -Name "InmoLegalPool"
```

---

## 🧪 Testing de URLs

Probar las siguientes URLs en el navegador:

1. ✅ **Página principal**
   ```
   https://oceanairti.sytes.net/inmolegal
   ```

2. ✅ **Formulario de contrato**
   ```
   https://oceanairti.sytes.net/inmolegal/contrato
   ```

3. ✅ **Widget JS**
   ```
   https://oceanairti.sytes.net/inmolegal/inmolegal-widget.js
   ```

4. ✅ **Panel Admin**
   ```
   https://oceanairti.sytes.net/inmolegal/admin/login
   ```

5. ✅ **API Endpoint**
   ```
   https://oceanairti.sytes.net/inmolegal/api/health
   ```

6. ✅ **Webhook Clip**
   ```
   https://oceanairti.sytes.net/inmolegal/webhook/clip
   ```

### Test desde PowerShell

```powershell
# Test básico
Invoke-WebRequest -Uri "https://oceanairti.sytes.net/inmolegal" -UseBasicParsing

# Test con headers
$headers = @{
    "Accept" = "application/json"
}
Invoke-WebRequest -Uri "https://oceanairti.sytes.net/inmolegal/contrato" -Headers $headers -UseBasicParsing
```

---

## 🐛 Troubleshooting

### Error 500 - Internal Server Error

1. **Verificar logs de Laravel**:
   ```powershell
   Get-Content "C:\inetpub\wwwroot\inmolegal\storage\logs\laravel.log" -Tail 50
   ```

2. **Habilitar debug temporalmente** en `.env`:
   ```env
   APP_DEBUG=true
   ```

3. **Verificar permisos**:
   ```powershell
   icacls "C:\inetpub\wwwroot\inmolegal\storage"
   icacls "C:\inetpub\wwwroot\inmolegal\bootstrap\cache"
   ```

### Error 404 - Not Found

1. **Verificar que URL Rewrite esté instalado**
2. **Revisar web.config** en `public` folder
3. **Verificar la ruta física** en IIS Manager
4. **Limpiar cache de Laravel**

### Rutas CSS/JS no cargan

1. **Verificar APP_URL** en `.env` incluye `/inmolegal`
2. **Regenerar assets**:
   ```powershell
   npm run build
   ```
3. **Verificar enlace simbólico de storage**:
   ```powershell
   php artisan storage:link
   ```

### Webhook de Clip no funciona

1. **Verificar URL del webhook** en panel de Clip:
   ```
   https://oceanairti.sytes.net/inmolegal/webhook/clip
   ```

2. **Verificar logs**:
   ```powershell
   Get-Content "C:\inetpub\wwwroot\inmolegal\storage\logs\laravel.log" | Select-String "webhook"
   ```

3. **Test manual del webhook**:
   ```powershell
   $body = @{
       event = "test"
   } | ConvertTo-Json
   
   Invoke-WebRequest -Uri "https://oceanairti.sytes.net/inmolegal/webhook/clip" `
       -Method POST `
       -Body $body `
       -ContentType "application/json"
   ```

---

## 📊 Monitoreo

### Ver logs en tiempo real

```powershell
# Laravel logs
Get-Content "C:\inetpub\wwwroot\inmolegal\storage\logs\laravel.log" -Wait -Tail 50

# IIS logs (ajustar ruta según tu instalación)
Get-Content "C:\inetpub\logs\LogFiles\W3SVC1\u_ex$(Get-Date -Format 'yyMMdd').log" -Wait -Tail 20
```

### Verificar estado de PHP

```powershell
# Ver procesos PHP
Get-Process php-cgi

# Ver Application Pool
Get-WebAppPoolState -Name "InmoLegalPool"
```

---

## ✅ Checklist Final

- [ ] web.config actualizado en carpeta `public`
- [ ] Aplicación creada en IIS Manager apuntando a `public`
- [ ] Application Pool configurado (si es aplicación)
- [ ] URL Rewrite Module instalado
- [ ] Permisos configurados en storage y bootstrap/cache
- [ ] .env tiene APP_URL correcto con `/inmolegal`
- [ ] Cache de Laravel limpiado
- [ ] IIS reiniciado
- [ ] URLs probadas y funcionando
- [ ] SSL/HTTPS configurado (si aplica)
- [ ] Webhook de Clip configurado con nueva URL
- [ ] Emails de prueba funcionando

---

## 🔗 URLs de Configuración de Clip

Actualizar en el panel de Clip:

- **Webhook URL**: `https://oceanairti.sytes.net/inmolegal/webhook/clip`
- **Return URL**: `https://oceanairti.sytes.net/inmolegal/contrato/confirmacion`
- **Cancel URL**: `https://oceanairti.sytes.net/inmolegal/contrato/cancelado`

---

## 📝 Notas Importantes

1. **APP_URL** debe incluir el subdirectorio `/inmolegal`
2. **web.config** DEBE estar en la carpeta `public`, no en la raíz
3. **IIS debe apuntar** a la carpeta `public`, no a la raíz del proyecto
4. **Permisos** son críticos para storage y bootstrap/cache
5. **Cache** debe limpiarse después de cada cambio en configuración
6. **HTTPS** es obligatorio para webhooks de Clip en producción

---

## 🆘 Soporte

Si tienes problemas:

1. Revisa logs de Laravel: `storage/logs/laravel.log`
2. Revisa logs de IIS: `C:\inetpub\logs\LogFiles`
3. Verifica que PHP esté funcionando: crea `info.php` con `<?php phpinfo(); ?>`
4. Usa herramientas de debug del navegador (F12)

---

**Última actualización**: Noviembre 4, 2025
**Versión Laravel**: 10.x
**IIS**: 10.0+
**PHP**: 8.1+
