# Guía de Configuración IIS para InmoLegal

## 📋 Requisitos Previos

- Windows Server 2016+ o Windows 10/11 Pro
- IIS 10.0 o superior
- PHP 8.1+ instalado
- PostgreSQL instalado y corriendo
- Extensiones PHP necesarias habilitadas

---

## 🔧 Instalación de Componentes IIS

### 1. Habilitar IIS con características necesarias

```powershell
# Ejecutar como Administrador en PowerShell
Enable-WindowsOptionalFeature -Online -FeatureName IIS-WebServerRole
Enable-WindowsOptionalFeature -Online -FeatureName IIS-WebServer
Enable-WindowsOptionalFeature -Online -FeatureName IIS-CommonHttpFeatures
Enable-WindowsOptionalFeature -Online -FeatureName IIS-HttpErrors
Enable-WindowsOptionalFeature -Online -FeatureName IIS-ApplicationDevelopment
Enable-WindowsOptionalFeature -Online -FeatureName IIS-HealthAndDiagnostics
Enable-WindowsOptionalFeature -Online -FeatureName IIS-Performance
Enable-WindowsOptionalFeature -Online -FeatureName IIS-Security
Enable-WindowsOptionalFeature -Online -FeatureName IIS-RequestFiltering
Enable-WindowsOptionalFeature -Online -FeatureName IIS-CGI
```

### 2. Instalar URL Rewrite Module

1. Descargar desde: https://www.iis.net/downloads/microsoft/url-rewrite
2. Ejecutar instalador: `rewrite_amd64_es-ES.msi`
3. Verificar instalación en IIS Manager

---

## 📁 Configuración del Sitio

### 1. Preparar la aplicación

```powershell
# Navegar a la carpeta del proyecto
cd C:\inetpub\wwwroot\inmolegal

# Instalar dependencias de Composer
composer install --optimize-autoloader --no-dev

# Generar clave de aplicación
php artisan key:generate

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear enlaces simbólicos
php artisan storage:link
```

### 2. Configurar permisos de carpetas

```powershell
# Dar permisos de escritura a IIS_IUSRS
icacls "C:\inetpub\wwwroot\inmolegal\storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\inetpub\wwwroot\inmolegal\bootstrap\cache" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

### 3. Crear sitio en IIS

1. Abrir **IIS Manager**
2. Click derecho en **Sites** → **Add Website**
3. Configurar:
   - **Site name**: InmoLegal
   - **Physical path**: `C:\inetpub\wwwroot\inmolegal\public`
   - **Binding**: 
     - Type: http
     - IP: All Unassigned
     - Port: 80
     - Host name: tu-dominio.com
4. Click **OK**

---

## ⚙️ Configuración de web.config

Crear archivo `web.config` en la carpeta `public`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <!-- URL Rewrite Rules -->
        <rewrite>
            <rules>
                <!-- Remover public de la URL -->
                <rule name="Laravel" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="false" />
                    <conditions>
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php/{R:1}" />
                </rule>
            </rules>
        </rewrite>

        <!-- Default Document -->
        <defaultDocument>
            <files>
                <clear />
                <add value="index.php" />
            </files>
        </defaultDocument>

        <!-- Handler Mappings -->
        <handlers>
            <remove name="PHP_via_FastCGI" />
            <add name="PHP_via_FastCGI" path="*.php" verb="*" modules="FastCgiModule" 
                 scriptProcessor="C:\php\php-cgi.exe" resourceType="Either" requireAccess="Script" />
        </handlers>

        <!-- Security -->
        <security>
            <requestFiltering>
                <hiddenSegments>
                    <add segment=".env" />
                    <add segment=".git" />
                    <add segment="storage" />
                    <add segment="bootstrap" />
                    <add segment="vendor" />
                </hiddenSegments>
                <fileExtensions>
                    <add fileExtension=".env" allowed="false" />
                </fileExtensions>
            </requestFiltering>
        </security>

        <!-- HTTP Headers -->
        <httpProtocol>
            <customHeaders>
                <add name="X-Frame-Options" value="SAMEORIGIN" />
                <add name="X-Content-Type-Options" value="nosniff" />
                <add name="X-XSS-Protection" value="1; mode=block" />
                <add name="Referrer-Policy" value="strict-origin-when-cross-origin" />
            </customHeaders>
        </httpProtocol>

        <!-- Compression -->
        <urlCompression doStaticCompression="true" doDynamicCompression="true" />

        <!-- HTTP Errors -->
        <httpErrors errorMode="Detailed" />
    </system.webServer>
</configuration>
```

---

## 🔐 Configuración de PHP en IIS

### 1. Verificar extensiones PHP habilitadas en php.ini

```ini
extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=openssl
extension=pdo_pgsql
extension=pgsql
extension=zip
```

### 2. Configurar FastCGI

1. Abrir **IIS Manager**
2. Seleccionar servidor → **FastCGI Settings**
3. Agregar aplicación:
   - **Full Path**: `C:\php\php-cgi.exe`
   - **Arguments**: (vacío)
   - **Max Instances**: 4
   - **Activity Timeout**: 600
   - **Request Timeout**: 600

---

## 🌐 Configuración de .env para Producción

```env
APP_NAME=InmoLegal
APP_ENV=production
APP_KEY=base64:TU_CLAVE_AQUI
APP_DEBUG=false
APP_URL=https://tu-dominio.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=badilloDB
DB_USERNAME=postgres
DB_PASSWORD=TU_PASSWORD_AQUI

MAIL_MAILER=smtp
MAIL_HOST=smtp.tuservidor.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@dominio.com
MAIL_PASSWORD=TU_PASSWORD_EMAIL
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="contratos@tu-dominio.com"
MAIL_FROM_NAME="${APP_NAME}"

# Clip Payment Gateway - PRODUCCIÓN
CLIP_API_KEY=TU_API_KEY_REAL
CLIP_SECRET_KEY=TU_SECRET_KEY_REAL
CLIP_API_URL=https://api.payclip.com
CLIP_ENVIRONMENT=production

# Admin Panel
ADMIN_PASSWORD=TU_PASSWORD_SEGURA_AQUI

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

---

## 🚀 Configuración SSL/HTTPS

### Opción 1: Certificado Let's Encrypt (Gratuito)

1. Instalar **Win-ACME**:
   ```powershell
   # Descargar desde: https://www.win-acme.com/
   # Ejecutar wacs.exe
   ```

2. Seguir wizard para generar certificado

### Opción 2: Certificado Comercial

1. Comprar certificado SSL
2. En IIS Manager → Seleccionar sitio → **Bindings**
3. Agregar binding HTTPS:
   - Type: https
   - Port: 443
   - SSL Certificate: Seleccionar certificado instalado

---

## 📊 Configuración de Application Pool

1. Abrir **IIS Manager**
2. **Application Pools** → Seleccionar pool de InmoLegal
3. Configurar:
   - **.NET CLR Version**: No Managed Code
   - **Managed Pipeline Mode**: Integrated
   - **Start Mode**: AlwaysRunning
   - **Identity**: ApplicationPoolIdentity (recomendado)
4. Advanced Settings:
   - **Idle Time-out**: 20 minutos
   - **Recycling** → Regular Time Interval: 1740 minutos (29 horas)

---

## 🔧 Optimizaciones de Rendimiento

### 1. Habilitar Output Caching

En IIS Manager → Sitio → Output Caching:
- Habilitar cache para archivos estáticos (.js, .css, .jpg, .png, etc.)
- Configurar duración: 7 días

### 2. Habilitar Compresión

```powershell
# Habilitar compresión estática
Set-WebConfigurationProperty -Filter "/system.webServer/urlCompression" -Name "doStaticCompression" -Value $true -PSPath "IIS:\Sites\InmoLegal"

# Habilitar compresión dinámica
Set-WebConfigurationProperty -Filter "/system.webServer/urlCompression" -Name "doDynamicCompression" -Value $true -PSPath "IIS:\Sites\InmoLegal"
```

### 3. Cache de OPcache de PHP

En `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

---

## 🛡️ Seguridad

### 1. Ocultar versión de PHP

En `php.ini`:
```ini
expose_php = Off
```

### 2. Configurar límites de carga

En `php.ini`:
```ini
upload_max_filesize = 20M
post_max_size = 20M
memory_limit = 256M
max_execution_time = 300
```

### 3. Configurar firewall

```powershell
# Permitir solo puertos necesarios
New-NetFirewallRule -DisplayName "HTTP" -Direction Inbound -LocalPort 80 -Protocol TCP -Action Allow
New-NetFirewallRule -DisplayName "HTTPS" -Direction Inbound -LocalPort 443 -Protocol TCP -Action Allow
```

---

## 📝 Mantenimiento

### Logs de Laravel

```powershell
# Ver logs en tiempo real
Get-Content "C:\inetpub\wwwroot\inmolegal\storage\logs\laravel.log" -Wait -Tail 50
```

### Limpiar cache

```powershell
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Backup de Base de Datos

```powershell
# Crear backup diario automático
pg_dump -U postgres -d badilloDB > "C:\backups\badilloDB_$(Get-Date -Format 'yyyyMMdd').sql"
```

---

## 🧪 Testing

### Verificar que el sitio funciona

```powershell
# Test desde PowerShell
Invoke-WebRequest -Uri "http://tu-dominio.com" -UseBasicParsing

# Test del formulario
Invoke-WebRequest -Uri "http://tu-dominio.com/contrato" -UseBasicParsing

# Test del widget
Invoke-WebRequest -Uri "http://tu-dominio.com/inmolegal-widget.js" -UseBasicParsing

# Test del panel admin
Invoke-WebRequest -Uri "http://tu-dominio.com/admin/login" -UseBasicParsing
```

---

## ❗ Troubleshooting

### Error 500 Internal Server Error

1. Verificar permisos de `storage` y `bootstrap/cache`
2. Verificar que `web.config` existe en `public`
3. Revisar logs: `storage/logs/laravel.log`
4. Habilitar `APP_DEBUG=true` temporalmente

### Error 404 Not Found

1. Verificar que URL Rewrite Module está instalado
2. Verificar `web.config` en carpeta `public`
3. Verificar que el sitio apunta a la carpeta `public`

### PHP no ejecuta

1. Verificar Handler Mapping en IIS
2. Verificar ruta de `php-cgi.exe` en `web.config`
3. Verificar FastCGI Settings

### Base de datos no conecta

1. Verificar que PostgreSQL está corriendo
2. Verificar credenciales en `.env`
3. Verificar extensión `pdo_pgsql` habilitada
4. Verificar firewall de PostgreSQL (puerto 5432)

---

## 📞 URLs Importantes del Sitio

- **Formulario**: `https://tu-dominio.com/contrato`
- **Widget JS**: `https://tu-dominio.com/inmolegal-widget.js`
- **Ejemplos**: `https://tu-dominio.com/widget-examples.html`
- **Admin Login**: `https://tu-dominio.com/admin/login`
- **Admin Panel**: `https://tu-dominio.com/admin`
- **Webhook Clip**: `https://tu-dominio.com/webhook/clip`
- **PDF Recibo**: `https://tu-dominio.com/pdf/recibo/{token}`
- **PDF Contrato**: `https://tu-dominio.com/pdf/contrato/{token}`

---

## ✅ Checklist de Deployment

- [ ] IIS instalado con URL Rewrite
- [ ] PHP 8.1+ instalado y configurado
- [ ] PostgreSQL instalado y corriendo
- [ ] Composer install ejecutado
- [ ] Permisos configurados en storage y bootstrap/cache
- [ ] .env configurado para producción
- [ ] APP_KEY generada
- [ ] web.config creado en public
- [ ] Sitio creado en IIS apuntando a /public
- [ ] Application Pool configurado
- [ ] SSL/HTTPS configurado
- [ ] Cache de Laravel generado
- [ ] Webhook de Clip configurado
- [ ] SMTP configurado para emails
- [ ] Contraseña de admin cambiada
- [ ] Testing completo realizado
- [ ] Backup automático configurado
