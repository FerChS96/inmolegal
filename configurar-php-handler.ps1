# Configurar Handler PHP a nivel de IIS (no en web.config)
# EJECUTAR COMO ADMINISTRADOR

Write-Host "=== Configurando PHP Handler a nivel de IIS ===" -ForegroundColor Cyan
Write-Host ""

Import-Module WebAdministration

$phpPath = "C:\php\php-cgi.exe"

# 1. Desbloquear sección de handlers (si está bloqueada)
Write-Host "1. Desbloqueando sección de handlers..." -ForegroundColor Yellow
try {
    Set-WebConfiguration //system.webServer/handlers -metadata overrideMode -value Allow -PSPath IIS:/ 
    Write-Host "   ✓ Sección desbloqueada" -ForegroundColor Green
} catch {
    Write-Host "   ! Ya estaba desbloqueada o error: $($_.Exception.Message)" -ForegroundColor Gray
}

# 2. Configurar handler a nivel global
Write-Host ""
Write-Host "2. Configurando PHP handler a nivel global..." -ForegroundColor Yellow

# Remover handler existente si hay
try {
    Remove-WebHandler -Name "PHP_via_FastCGI" -PSPath "IIS:\" -ErrorAction SilentlyContinue
    Write-Host "   - Handler anterior removido" -ForegroundColor Gray
} catch {}

# Agregar handler nuevo
try {
    Add-WebHandler -Name "PHP_via_FastCGI" `
                   -Path "*.php" `
                   -Verb "*" `
                   -Modules "FastCgiModule" `
                   -ScriptProcessor $phpPath `
                   -ResourceType "Either" `
                   -RequiredAccess "Script" `
                   -PSPath "IIS:\"
    
    Write-Host "   ✓ Handler PHP configurado globalmente" -ForegroundColor Green
} catch {
    Write-Host "   ✗ Error: $($_.Exception.Message)" -ForegroundColor Red
}

# 3. Verificar configuración FastCGI
Write-Host ""
Write-Host "3. Verificando FastCGI..." -ForegroundColor Yellow
$fastCgiConfig = Get-WebConfiguration "/system.webServer/fastCgi/application[@fullPath='$phpPath']" -PSPath "MACHINE/WEBROOT/APPHOST"

if ($fastCgiConfig) {
    Write-Host "   ✓ FastCGI configurado" -ForegroundColor Green
    
    # Actualizar timeouts si son muy bajos
    if ($fastCgiConfig.activityTimeout -lt 600) {
        Write-Host "   - Actualizando timeouts..." -ForegroundColor Gray
        Set-WebConfigurationProperty "/system.webServer/fastCgi/application[@fullPath='$phpPath']" `
            -PSPath "MACHINE/WEBROOT/APPHOST" `
            -Name "activityTimeout" `
            -Value 600
        Set-WebConfigurationProperty "/system.webServer/fastCgi/application[@fullPath='$phpPath']" `
            -PSPath "MACHINE/WEBROOT/APPHOST" `
            -Name "requestTimeout" `
            -Value 600
        Write-Host "   ✓ Timeouts actualizados" -ForegroundColor Green
    }
} else {
    Write-Host "   ! Configurando FastCGI..." -ForegroundColor Yellow
    Add-WebConfiguration "/system.webServer/fastCgi" -PSPath "MACHINE/WEBROOT/APPHOST" -Value @{
        fullPath = $phpPath
        maxInstances = 4
        instanceMaxRequests = 10000
        activityTimeout = 600
        requestTimeout = 600
        protocol = "NamedPipe"
    }
    Write-Host "   ✓ FastCGI configurado" -ForegroundColor Green
}

# 4. Verificar extensiones PHP habilitadas
Write-Host ""
Write-Host "4. Verificando extensiones PHP críticas..." -ForegroundColor Yellow
$phpIniPath = "C:\php\php.ini"
$requiredExtensions = @("curl", "fileinfo", "mbstring", "openssl", "pdo_pgsql", "pgsql")

if (Test-Path $phpIniPath) {
    $phpIniContent = Get-Content $phpIniPath
    $disabledExtensions = @()
    
    foreach ($ext in $requiredExtensions) {
        $pattern = "^;?\s*extension\s*=\s*$ext"
        $line = $phpIniContent | Where-Object { $_ -match $pattern }
        
        if ($line -and $line -match "^;") {
            $disabledExtensions += $ext
        }
    }
    
    if ($disabledExtensions.Count -gt 0) {
        Write-Host "   ! Extensiones deshabilitadas encontradas:" -ForegroundColor Red
        foreach ($ext in $disabledExtensions) {
            Write-Host "     - $ext" -ForegroundColor Red
        }
        Write-Host ""
        Write-Host "   ACCIÓN REQUERIDA:" -ForegroundColor Yellow
        Write-Host "   Edita C:\php\php.ini y descomenta (quita el ;) de estas líneas:" -ForegroundColor Yellow
        foreach ($ext in $disabledExtensions) {
            Write-Host "   ;extension=$ext  ->  extension=$ext" -ForegroundColor Gray
        }
        Write-Host ""
        $continue = Read-Host "¿Quieres que intente habilitarlas automáticamente? (s/n)"
        if ($continue -eq "s") {
            $phpIniContent = Get-Content $phpIniPath -Raw
            foreach ($ext in $disabledExtensions) {
                $phpIniContent = $phpIniContent -replace "^;\s*extension\s*=\s*$ext", "extension=$ext"
            }
            $phpIniContent | Set-Content $phpIniPath
            Write-Host "   ✓ Extensiones habilitadas" -ForegroundColor Green
        }
    } else {
        Write-Host "   ✓ Extensiones requeridas habilitadas" -ForegroundColor Green
    }
} else {
    Write-Host "   ✗ php.ini NO encontrado" -ForegroundColor Red
}

# 5. Reiniciar IIS
Write-Host ""
Write-Host "5. Reiniciando IIS..." -ForegroundColor Yellow
iisreset /restart | Out-Null
Start-Sleep -Seconds 4
Write-Host "   ✓ IIS reiniciado" -ForegroundColor Green

# 6. Test
Write-Host ""
Write-Host "6. Probando sitio..." -ForegroundColor Yellow
Start-Sleep -Seconds 2

try {
    $response = Invoke-WebRequest -Uri "https://oceanairti.sytes.net/inmolegal/test.php" -UseBasicParsing -TimeoutSec 15
    Write-Host "   ✓ ¡ÉXITO! Código: $($response.StatusCode)" -ForegroundColor Green
    Write-Host ""
    Write-Host "=== ¡Sitio PHP funcionando correctamente! ===" -ForegroundColor Green
    Write-Host ""
    Write-Host "Ahora prueba Laravel:" -ForegroundColor Cyan
    Write-Host "https://oceanairti.sytes.net/inmolegal/" -ForegroundColor White
} catch {
    Write-Host "   ✗ Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "Verificando configuración adicional..." -ForegroundColor Yellow
    
    # Mostrar handlers configurados
    Write-Host ""
    Write-Host "Handlers configurados:" -ForegroundColor Gray
    Get-WebHandler -PSPath "IIS:\" | Where-Object { $_.Name -like "*PHP*" } | Format-Table Name, Path, ScriptProcessor -AutoSize
    
    Write-Host ""
    Write-Host "Si el problema persiste:" -ForegroundColor Yellow
    Write-Host "1. Abre IIS Manager" -ForegroundColor Gray
    Write-Host "2. Ve a 'Default Web Site' > 'inmolegal'" -ForegroundColor Gray
    Write-Host "3. Abre 'Handler Mappings'" -ForegroundColor Gray
    Write-Host "4. Verifica que existe 'PHP_via_FastCGI' para *.php" -ForegroundColor Gray
    Write-Host "5. Si no existe, agrégalo manualmente" -ForegroundColor Gray
}

Write-Host ""
