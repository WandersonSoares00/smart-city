@echo off
REM Smart City - Script para iniciar todos os componentes
REM Este script abre múltiplas janelas de terminal para rodar:
REM - Gateway
REM - Sensor de Temperatura
REM - Câmera
REM - Semáforo
REM - Client Web

echo.
echo ╔═══════════════════════════════════════════════════════╗
echo ║   🏙️  Smart City - Inicializando todos os serviços   ║
echo ╚═══════════════════════════════════════════════════════╝
echo.

echo [1/5] Iniciando Gateway...
start "Smart City - Gateway" cmd /k "cd /d %cd% && php gateway/run.php"
timeout /t 3 /nobreak

echo [2/5] Iniciando Sensor de Temperatura...
start "Smart City - Temperature Sensor" cmd /k "cd /d %cd% && php devices/bin/run-temperature-sensor.php"
timeout /t 2 /nobreak

echo [3/5] Iniciando Câmera...
start "Smart City - Camera" cmd /k "cd /d %cd% && php devices/bin/run-camera.php"
timeout /t 2 /nobreak

echo [4/5] Iniciando Semáforo...
start "Smart City - Traffic Light" cmd /k "cd /d %cd% && php devices/bin/run-traffic-light.php"
timeout /t 2 /nobreak

echo [5/5] Iniciando Client Web...
start "Smart City - Web Client" cmd /k "cd /d %cd%\client-web && npm start"

echo.
echo ╔═══════════════════════════════════════════════════════╗
echo ║   ✅ Todos os serviços foram iniciados!              ║
echo ║   🌐 Acesse: http://localhost:3000                   ║
echo ║   ⏱️  Aguarde 5-10 segundos para carregar...         ║
echo ╚═══════════════════════════════════════════════════════╝
echo.

pause
