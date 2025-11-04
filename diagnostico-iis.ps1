# Script de Diagnóstico y Corrección IIS para InmoLegal
# EJECUTAR COMO ADMINISTRADOR

Write-Host "=== Diagnóstico IIS para InmoLegal ===" -ForegroundColor Cyan
Write-Host ""

# 1. Verificar si IIS está corriendo
Write-Host "1. Verificando estado de IIS..." -ForegroundColor Yellow
$iisService = Get-Service W3SVC -ErrorAction SilentlyContinue
if ($iisService.Status -eq "Running") {
    Write-Host "   ✓ IIS está corriendo" -ForegroundColor Green
} else {
    Write-Host "   ✗ IIS NO está corriendo" -ForegroundColor Red
    Write-Host "   Iniciando IIS..." -ForegroundColor Yellow
    Start-Service W3SVC
}

# 2. Importar módulo WebAdministration
Write-Host ""
Write-Host "2. Cargando módulo IIS..." -ForegroundColor Yellow
try {
    Import-Module WebAdministration -ErrorAction Stop
    Write-Host "   ✓ Módulo cargado" -ForegroundColor Green
} catch {
    Write-Host "   ✗ Error al cargar módulo: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# 3. Verificar si existe Default Web Site
Write-Host ""
Write-Host "3. Verificando Default Web Site..." -ForegroundColor Yellow
$defaultSite = Get-Website -Name "Default Web Site" -ErrorAction SilentlyContinue
if ($defaultSite) {
    Write-Host "   ✓ Default Web Site existe" -ForegroundColor Green
    Write-Host "   - Estado: $($defaultSite.State)" -ForegroundColor Gray
    Write-Host "   - Path: $($defaultSite.PhysicalPath)" -ForegroundColor Gray
} else {
    Write-Host "   ✗ Default Web Site NO encontrado" -ForegroundColor Red
}

# 4. Verificar aplicación /inmolegal
Write-Host ""
Write-Host "4. Verificando aplicación /inmolegal..." -ForegroundColor Yellow
$app = Get-WebApplication -Site "Default Web Site" -Name "inmolegal" -ErrorAction SilentlyContinue
if ($app) {
    Write-Host "   ✓ Aplicación existe" -ForegroundColor Green
    Write-Host "   - Path: $($app.Path)" -ForegroundColor Gray
    Write-Host "   - Physical Path: $($app.PhysicalPath)" -ForegroundColor Gray
    Write-Host "   - Application Pool: $($app.ApplicationPool)" -ForegroundColor Gray
    
    # Verificar si apunta a public
    if ($app.PhysicalPath -like "*\public") {
        Write-Host "   ✓ Apunta correctamente a carpeta public" -ForegroundColor Green
    } else {
        Write-Host "   ✗ NO apunta a carpeta public" -ForegroundColor Red
        Write-Host "   Corrigiendo..." -ForegroundColor Yellow
        Set-WebConfigurationProperty -Filter "/system.applicationHost/sites/site[@name='Default Web Site']/application[@path='/inmolegal']" `
            -Name "physicalPath" -Value "C:\inetpub\wwwroot\inmolegal\public"
        Write-Host "   ✓ Ruta corregida" -ForegroundColor Green
    }
} else {
    Write-Host "   ✗ Aplicación NO existe" -ForegroundColor Red
    Write-Host "   Creando aplicación..." -ForegroundColor Yellow
    
    try {
        # Crear Application Pool
        $poolName = "InmoLegalPool"
        if (!(Test-Path "IIS:\AppPools\$poolName")) {
            $pool = New-WebAppPool -Name $poolName
            Set-ItemProperty "IIS:\AppPools\$poolName" -Name managedRuntimeVersion -Value ""
            Set-ItemProperty "IIS:\AppPools\$poolName" -Name managedPipelineMode -Value "Integrated"
            Write-Host "   ✓ Application Pool '$poolName' creado" -ForegroundColor Green
        }
        
        # Crear aplicación
        New-WebApplication -Name "inmolegal" `
            -Site "Default Web Site" `
            -PhysicalPath "C:\inetpub\wwwroot\inmolegal\public" `
            -ApplicationPool $poolName -ErrorAction Stop
        Write-Host "   ✓ Aplicación creada exitosamente" -ForegroundColor Green
    } catch {
        Write-Host "   ✗ Error al crear aplicación: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# 5. Verificar URL Rewrite Module
Write-Host ""
Write-Host "5. Verificando URL Rewrite Module..." -ForegroundColor Yellow
$rewriteModule = Get-WindowsFeature -Name "IIS-URLRewrite" -ErrorAction SilentlyContinue
if ($rewriteModule -and $rewriteModule.Installed) {
    Write-Host "   ✓ URL Rewrite instalado" -ForegroundColor Green
} else {
    Write-Host "   ! URL Rewrite NO está instalado" -ForegroundColor Red
    Write-Host "   Descárgalo desde: https://www.iis.net/downloads/microsoft/url-rewrite" -ForegroundColor Yellow
}

# 6. Verificar Handler PHP
Write-Host ""
Write-Host "6. Verificando PHP Handler..." -ForegroundColor Yellow
$phpPath = "C:\php\php-cgi.exe"
if (Test-Path $phpPath) {
    Write-Host "   ✓ PHP encontrado en: $phpPath" -ForegroundColor Green
    
    # Verificar versión
    $phpVersion = & $phpPath -v 2>&1 | Select-String "PHP (\d+\.\d+\.\d+)" | ForEach-Object { $_.Matches.Groups[1].Value }
    Write-Host "   - Versión: $phpVersion" -ForegroundColor Gray
} else {
    Write-Host "   ✗ PHP NO encontrado en: $phpPath" -ForegroundColor Red
    Write-Host "   Buscar en otras ubicaciones..." -ForegroundColor Yellow
    $phpLocations = @("C:\Program Files\PHP", "C:\PHP", "C:\php-8.1", "C:\php-8.2")
    foreach ($loc in $phpLocations) {
        if (Test-Path "$loc\php-cgi.exe") {
            Write-Host "   ! PHP encontrado en: $loc" -ForegroundColor Yellow
            Write-Host "   Actualiza web.config con esta ruta" -ForegroundColor Yellow
        }
    }
}

# 7. Verificar permisos
Write-Host ""
Write-Host "7. Verificando permisos..." -ForegroundColor Yellow
$storagePath = "C:\inetpub\wwwroot\inmolegal\storage"
$cachePath = "C:\inetpub\wwwroot\inmolegal\bootstrap\cache"

try {
    $storageAcl = Get-Acl $storagePath
    $hasIISPermission = $storageAcl.Access | Where-Object { $_.IdentityReference -like "*IIS_IUSRS*" }
    if ($hasIISPermission) {
        Write-Host "   ✓ IIS_IUSRS tiene permisos en storage" -ForegroundColor Green
    } else {
        Write-Host "   ! Configurando permisos en storage..." -ForegroundColor Yellow
        icacls $storagePath /grant "IIS_IUSRS:(OI)(CI)F" /T | Out-Null
        icacls $cachePath /grant "IIS_IUSRS:(OI)(CI)F" /T | Out-Null
        Write-Host "   ✓ Permisos configurados" -ForegroundColor Green
    }
} catch {
    Write-Host "   ✗ Error al verificar permisos: $($_.Exception.Message)" -ForegroundColor Red
}

# 8. Verificar archivos críticos
Write-Host ""
Write-Host "8. Verificando archivos críticos..." -ForegroundColor Yellow
$criticalFiles = @(
    "C:\inetpub\wwwroot\inmolegal\public\index.php",
    "C:\inetpub\wwwroot\inmolegal\public\web.config",
    "C:\inetpub\wwwroot\inmolegal\.env",
    "C:\inetpub\wwwroot\inmolegal\vendor\autoload.php"
)
foreach ($file in $criticalFiles) {
    if (Test-Path $file) {
        Write-Host "   ✓ $(Split-Path $file -Leaf)" -ForegroundColor Green
    } else {
        Write-Host "   ✗ $(Split-Path $file -Leaf) NO encontrado" -ForegroundColor Red
    }
}

# 9. Reiniciar Application Pool
Write-Host ""
Write-Host "9. Reiniciando Application Pool..." -ForegroundColor Yellow
$app = Get-WebApplication -Site "Default Web Site" -Name "inmolegal" -ErrorAction SilentlyContinue
if ($app -and $app.ApplicationPool) {
    try {
        Restart-WebAppPool -Name $app.ApplicationPool
        Write-Host "   ✓ Application Pool reiniciado" -ForegroundColor Green
    } catch {
        Write-Host "   ! Error al reiniciar: $($_.Exception.Message)" -ForegroundColor Yellow
    }
}

# 10. Test de conectividad
Write-Host ""
Write-Host "10. Probando URL..." -ForegroundColor Yellow
Start-Sleep -Seconds 2
try {
    $response = Invoke-WebRequest -Uri "https://oceanairti.sytes.net/inmolegal/test.php" -UseBasicParsing -TimeoutSec 10
    Write-Host "   ✓ Sitio responde: $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "   ✗ Sitio NO responde correctamente" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Gray
}

Write-Host ""
Write-Host "=== Diagnóstico Completado ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "Si el sitio aún no funciona, revisa:" -ForegroundColor Yellow
Write-Host "1. Logs de IIS en: C:\inetpub\logs\LogFiles" -ForegroundColor Gray
Write-Host "2. Event Viewer > Windows Logs > Application" -ForegroundColor Gray
Write-Host "3. Ejecuta: Get-Content C:\inetpub\wwwroot\inmolegal\storage\logs\laravel.log -Tail 50" -ForegroundColor Gray
Write-Host ""
