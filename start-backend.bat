@echo off
title Backend - Laravel (port 8001)
echo Inditom a Laravel backendet...
echo Host: 0.0.0.0  Port: 8001
echo.
cd /d "%~dp0backend"
php artisan serve --host=0.0.0.0 --port=8001
pause
