@echo off
title Frontend - Angular (port 4200)
echo Inditom az Angular frontendet...
echo Host: 0.0.0.0  Port: 4200
echo.
cd /d "%~dp0frontend"
npx ng serve --host 0.0.0.0 --port 4200
pause
