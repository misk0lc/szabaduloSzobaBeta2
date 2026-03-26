@echo off
setlocal

set "NO_PAUSE=0"
if /I "%~1"=="--no-pause" set "NO_PAUSE=1"

cd /d "%~dp0"
title Projekt inicializalas (clone utan)

echo ==================================================
echo   SzabaduloSzoba - Elso setup (clone utan)
echo ==================================================
echo.

where /q php
if errorlevel 1 (
    echo [HIBA] A PHP nincs a PATH-ban. Telepitsd a PHP 8.2+ verziot.
    goto :fail
)

where /q composer
if errorlevel 1 (
    echo [HIBA] A Composer nincs a PATH-ban. Telepitsd a Composer-t.
    goto :fail
)

where /q node
if errorlevel 1 (
    echo [HIBA] A Node.js nincs a PATH-ban. Telepitsd a Node.js LTS verziot.
    goto :fail
)

where /q npm
if errorlevel 1 (
    echo [HIBA] Az npm nem erheto el. Telepitsd ujra a Node.js-t.
    goto :fail
)

echo [1/3] Backend PHP fuggosegek telepitese...
pushd backend
call composer install
if errorlevel 1 (
    echo [HIBA] composer install sikertelen.
    popd
    goto :fail
)

if not exist .env (
    if exist .env.example (
        echo [INFO] .env letrehozasa .env.example alapjan...
        copy /Y .env.example .env >nul
    ) else (
        echo [FIGYELEM] Nem talalhato .env.example, ezt kezileg kell potolni.
    )
)

echo [2/3] Laravel inicializalas...
php artisan key:generate
if errorlevel 1 (
    echo [HIBA] php artisan key:generate sikertelen.
    popd
    goto :fail
)

php artisan migrate:fresh --seed --force
if errorlevel 1 (
    echo [FIGYELEM] A migrate:fresh --seed nem futott le sikeresen.
    echo [FIGYELEM] Ellenorizd a backend\.env fajlban a DB_* beallitasokat.
    echo [FIGYELEM] A setup ettol fuggetlenul folytatodik.
)

if exist package.json (
    echo [INFO] Backend Node fuggosegek telepitese...
    call npm install
    if errorlevel 1 (
        echo [FIGYELEM] backend npm install sikertelen, de a setup folytatodik.
    )
)
popd

echo [3/3] Frontend fuggosegek telepitese...
pushd frontend
call npm install
if errorlevel 1 (
    echo [HIBA] frontend npm install sikertelen.
    popd
    goto :fail
)
popd

echo.
echo ==================================================
echo Setup kesz.
echo Inditas:
echo   - start-backend.bat
echo   - start-frontend.bat
echo ==================================================
echo.
if "%NO_PAUSE%"=="0" pause
exit /b 0

:fail
echo.
echo A setup hiba miatt leallt.
echo.
if "%NO_PAUSE%"=="0" pause
exit /b 1
