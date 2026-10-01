@echo off
title Integrative Programming 2 - Setup
cd /d "%~dp0"
echo ==========================================================
echo   Integrative Programming 2 - Laravel project setup
echo ==========================================================
echo.

where php >nul 2>nul || (echo [ERROR] PHP not found. Open Laravel Herd once, then run this again. & pause & exit /b 1)
where composer >nul 2>nul || (echo [ERROR] Composer not found. Open Laravel Herd once, then run this again. & pause & exit /b 1)

echo [1/6] Installing PHP packages (Laravel + Sanctum)... this can take a few minutes
call composer install --no-interaction
if errorlevel 1 (echo [ERROR] composer install failed. & pause & exit /b 1)

echo.
echo [2/6] Creating .env file...
if not exist .env copy .env.example .env >nul
call php artisan key:generate --force

:checkdb
echo.
echo [3/6] Creating MySQL database "integrative_db"...
php -r "try { $p = new PDO('mysql:host=127.0.0.1;port=3306', 'root', ''); $p->exec('CREATE DATABASE IF NOT EXISTS integrative_db'); echo 'Database ready.'; } catch (Exception $e) { echo 'FAIL: ' . $e->getMessage(); exit(1); }"
if errorlevel 1 (
    echo.
    echo [!] Could not connect to MySQL.
    echo     Open the XAMPP Control Panel and click "Start" next to MySQL,
    echo     then press any key here to try again.
    pause >nul
    goto checkdb
)

echo.
echo [4/6] Creating tables and sample data...
call php artisan migrate:fresh --seed --force
if errorlevel 1 (echo [ERROR] Migration failed. & pause & exit /b 1)

echo.
echo [5/6] Running automated tests...
call php artisan test

echo.
echo [6/6] Starting the API server...
echo ----------------------------------------------------------
echo   API base URL : http://127.0.0.1:8000/api
echo   Import into Postman: docs\Integrative-Programming-2.postman_collection.json
echo   Keep this window open while testing. Press Ctrl+C to stop.
echo ----------------------------------------------------------
call php artisan serve
pause
