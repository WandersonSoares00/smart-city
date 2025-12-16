# Smart City - Script PowerShell para iniciar todos os componentes
# Execute como: .\start-all.ps1

Write-Host ""
Write-Host "╔═══════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║   🏙️  Smart City - Inicializando todos os serviços   ║" -ForegroundColor Cyan
Write-Host "╚═══════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

$scriptPath = Split-Path -Parent $MyInvocation.MyCommand.Path

# Gateway
Write-Host "[1/5] Iniciando Gateway..." -ForegroundColor Yellow
Start-Process powershell -ArgumentList "-NoExit -Command `"cd '$scriptPath'; php gateway/run.php`"" -WindowStyle Normal
Start-Sleep -Seconds 3

# Sensor de Temperatura
Write-Host "[2/5] Iniciando Sensor de Temperatura..." -ForegroundColor Yellow
Start-Process powershell -ArgumentList "-NoExit -Command `"cd '$scriptPath'; php devices/bin/run-temperature-sensor.php`"" -WindowStyle Normal
Start-Sleep -Seconds 2

# Câmera
Write-Host "[3/5] Iniciando Câmera..." -ForegroundColor Yellow
Start-Process powershell -ArgumentList "-NoExit -Command `"cd '$scriptPath'; php devices/bin/run-camera.php`"" -WindowStyle Normal
Start-Sleep -Seconds 2

# Semáforo
Write-Host "[4/5] Iniciando Semáforo..." -ForegroundColor Yellow
Start-Process powershell -ArgumentList "-NoExit -Command `"cd '$scriptPath'; php devices/bin/run-traffic-light.php`"" -WindowStyle Normal
Start-Sleep -Seconds 2

# Client Web
Write-Host "[5/5] Iniciando Client Web..." -ForegroundColor Yellow
Start-Process powershell -ArgumentList "-NoExit -Command `"cd '$scriptPath\client-web'; npm start`"" -WindowStyle Normal

Write-Host ""
Write-Host "╔═══════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║   ✅ Todos os serviços foram iniciados!              ║" -ForegroundColor Green
Write-Host "║   🌐 Acesse: http://localhost:3000                   ║" -ForegroundColor Green
Write-Host "║   ⏱️  Aguarde 5-10 segundos para carregar...         ║" -ForegroundColor Green
Write-Host "╚═══════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""
