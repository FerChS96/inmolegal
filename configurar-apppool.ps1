# Crear Application Pool dedicado y cambiar aplicación
# EJECUTAR COMO ADMINISTRADOR

Write-Host "Configurando Application Pool dedicado para InmoLegal..." -ForegroundColor Cyan

Import-Module WebAdministration

# 1. Crear Application Pool si no existe
$poolName = "InmoLegalPool"
if (!(Test-Path "IIS:\AppPools\$poolName")) {
    Write-Host "Creando Application Pool..." -ForegroundColor Yellow
    $pool = New-WebAppPool -Name $poolName
    
    # Configurar pool para PHP (No Managed Code)
    Set-ItemProperty "IIS:\AppPools\$poolName" -Name managedRuntimeVersion -Value ""
    Set-ItemProperty "IIS:\AppPools\$poolName" -Name managedPipelineMode -Value "Integrated"
    
    Write-Host "✓ Application Pool creado" -ForegroundColor Green
} else {
    Write-Host "Application Pool ya existe" -ForegroundColor Green
}

# 2. Cambiar la aplicación para usar el nuevo pool
Write-Host "Asignando Application Pool a la aplicación..." -ForegroundColor Yellow
Set-ItemProperty "IIS:\Sites\Default Web Site\inmolegal" -Name applicationPool -Value $poolName
Write-Host "✓ Application Pool asignado" -ForegroundColor Green

# 3. Dar permisos al Application Pool
Write-Host "Configurando permisos para el Application Pool..." -ForegroundColor Yellow
$paths = @(
    "C:\inetpub\wwwroot\inmolegal\storage",
    "C:\inetpub\wwwroot\inmolegal\bootstrap\cache"
)

foreach ($path in $paths) {
    icacls $path /grant "IIS APPPOOL\$poolName`:(OI)(CI)F" /T | Out-Null
}
Write-Host "✓ Permisos configurados" -ForegroundColor Green

# 4. Reiniciar el pool
Write-Host "Reiniciando Application Pool..." -ForegroundColor Yellow
Restart-WebAppPool -Name $poolName
Start-Sleep -Seconds 2
Write-Host "✓ Application Pool reiniciado" -ForegroundColor Green

Write-Host ""
Write-Host "=== Configuración completada ===" -ForegroundColor Cyan
Write-Host "Probando sitio..." -ForegroundColor Yellow

Start-Sleep -Seconds 2
try {
    $response = Invoke-WebRequest -Uri "https://oceanairti.sytes.net/inmolegal/test.php" -UseBasicParsing -TimeoutSec 10
    Write-Host "✓ ÉXITO! Sitio responde con código: $($response.StatusCode)" -ForegroundColor Green
} catch {
    Write-Host "✗ Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "Revisa los logs de IIS para más detalles" -ForegroundColor Yellow
}
