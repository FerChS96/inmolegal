# Reconfigurar FastCGI para PHP en IIS
# EJECUTAR COMO ADMINISTRADOR

Write-Host "=== Reconfigurando FastCGI para PHP ===" -ForegroundColor Cyan
Write-Host ""

Import-Module WebAdministration

$phpPath = "C:\php\php-cgi.exe"

# 1. Verificar que PHP existe
Write-Host "1. Verificando PHP..." -ForegroundColor Yellow
if (Test-Path $phpPath) {
    Write-Host "   ✓ PHP encontrado: $phpPath" -ForegroundColor Green
} else {
    Write-Host "   ✗ PHP NO encontrado en: $phpPath" -ForegroundColor Red
    exit 1
}

# 2. Verificar configuración de FastCGI
Write-Host ""
Write-Host "2. Verificando configuración FastCGI..." -ForegroundColor Yellow
$fastCgiPath = "/system.webServer/fastCgi/application[@fullPath='$phpPath']"
$fastCgiConfig = Get-WebConfiguration $fastCgiPath -PSPath "MACHINE/WEBROOT/APPHOST"

if ($fastCgiConfig) {
    Write-Host "   ✓ FastCGI ya configurado" -ForegroundColor Green
    Write-Host "   - Max Instances: $($fastCgiConfig.maxInstances)" -ForegroundColor Gray
    Write-Host "   - Activity Timeout: $($fastCgiConfig.activityTimeout)" -ForegroundColor Gray
} else {
    Write-Host "   ! FastCGI NO configurado, agregando..." -ForegroundColor Yellow
    
    # Agregar configuración FastCGI
    Add-WebConfiguration "/system.webServer/fastCgi" -PSPath "MACHINE/WEBROOT/APPHOST" -Value @{
        fullPath = $phpPath
        maxInstances = 4
        instanceMaxRequests = 10000
        activityTimeout = 600
        requestTimeout = 600
        protocol = "NamedPipe"
        flushNamedPipe = $false
    }
    
    Write-Host "   ✓ FastCGI configurado" -ForegroundColor Green
}

# 3. Verificar Handler Mapping a nivel de sitio
Write-Host ""
Write-Host "3. Verificando Handler Mapping..." -ForegroundColor Yellow
$handlerPath = "/system.webServer/handlers"
$handler = Get-WebConfiguration "$handlerPath/add[@name='PHP_via_FastCGI']" -PSPath "IIS:\Sites\Default Web Site\inmolegal"

if ($handler) {
    Write-Host "   ✓ Handler existe" -ForegroundColor Green
} else {
    Write-Host "   ! Handler NO existe, agregando..." -ForegroundColor Yellow
    
    # Remover handler si existe
    try {
        Remove-WebConfigurationProperty -PSPath "IIS:\Sites\Default Web Site\inmolegal" -Filter "$handlerPath" -Name "." -AtElement @{name='PHP_via_FastCGI'} -ErrorAction SilentlyContinue
    } catch {}
    
    # Agregar handler
    Add-WebConfiguration -PSPath "IIS:\Sites\Default Web Site\inmolegal" -Filter "$handlerPath" -Value @{
        name = 'PHP_via_FastCGI'
        path = '*.php'
        verb = '*'
        modules = 'FastCgiModule'
        scriptProcessor = $phpPath
        resourceType = 'Either'
        requireAccess = 'Script'
    }
    
    Write-Host "   ✓ Handler agregado" -ForegroundColor Green
}

# 4. Habilitar errores detallados en PHP
Write-Host ""
Write-Host "4. Verificando configuración de errores en php.ini..." -ForegroundColor Yellow
$phpIniPath = "C:\php\php.ini"
if (Test-Path $phpIniPath) {
    $phpIniContent = Get-Content $phpIniPath -Raw
    if ($phpIniContent -match "display_errors\s*=\s*Off") {
        Write-Host "   ! display_errors está Off (considera habilitarlo temporalmente para debug)" -ForegroundColor Yellow
    } else {
        Write-Host "   ✓ php.ini encontrado" -ForegroundColor Green
    }
} else {
    Write-Host "   ! php.ini NO encontrado en ubicación esperada" -ForegroundColor Yellow
}

# 5. Verificar extensiones PHP requeridas
Write-Host ""
Write-Host "5. Verificando extensiones PHP..." -ForegroundColor Yellow
$extensions = @(
    "extension=curl",
    "extension=fileinfo",
    "extension=mbstring",
    "extension=openssl",
    "extension=pdo_pgsql"
)

$phpIniContent = Get-Content $phpIniPath -ErrorAction SilentlyContinue
$missingExtensions = @()
foreach ($ext in $extensions) {
    if ($phpIniContent -notmatch $ext.Replace("extension=", "").Replace(";", "")) {
        $missingExtensions += $ext
    }
}

if ($missingExtensions.Count -eq 0) {
    Write-Host "   ✓ Extensiones básicas habilitadas" -ForegroundColor Green
} else {
    Write-Host "   ! Algunas extensiones pueden estar deshabilitadas:" -ForegroundColor Yellow
    foreach ($ext in $missingExtensions) {
        Write-Host "     - $ext" -ForegroundColor Gray
    }
}

# 6. Reiniciar IIS
Write-Host ""
Write-Host "6. Reiniciando IIS..." -ForegroundColor Yellow
try {
    iisreset /restart | Out-Null
    Start-Sleep -Seconds 3
    Write-Host "   ✓ IIS reiniciado" -ForegroundColor Green
} catch {
    Write-Host "   ! Error al reiniciar: $($_.Exception.Message)" -ForegroundColor Yellow
}

# 7. Test final
Write-Host ""
Write-Host "7. Probando sitio..." -ForegroundColor Yellow
Start-Sleep -Seconds 2

try {
    $response = Invoke-WebRequest -Uri "https://oceanairti.sytes.net/inmolegal/test.php" -UseBasicParsing -TimeoutSec 10
    Write-Host "   ✓ ÉXITO! Código: $($response.StatusCode)" -ForegroundColor Green
    Write-Host ""
    Write-Host "=== ¡Sitio funcionando! ===" -ForegroundColor Green
} catch {
    Write-Host "   ✗ Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "Revisando logs más recientes..." -ForegroundColor Yellow
    
    $logPath = Get-ChildItem "C:\inetpub\logs\LogFiles\W3SVC*" -Directory | Select-Object -First 1 -ExpandProperty FullName
    $latestLog = Get-Content "$logPath\*.log" -Tail 3 | Where-Object { $_ -like "*inmolegal*" }
    if ($latestLog) {
        Write-Host "Último log:" -ForegroundColor Gray
        Write-Host $latestLog -ForegroundColor Gray
    }
    
    Write-Host ""
    Write-Host "Acciones adicionales:" -ForegroundColor Yellow
    Write-Host "1. Verifica Event Viewer > Windows Logs > Application para errores PHP" -ForegroundColor Gray
    Write-Host "2. Verifica que el archivo test.php existe y tiene permisos de lectura" -ForegroundColor Gray
    Write-Host "3. Intenta acceder directamente: https://oceanairti.sytes.net/inmolegal/index.php" -ForegroundColor Gray
}

Write-Host ""
