@echo off
cd /d "%~dp0backend"

title Mobile Phone Repair Status Tracking System Launcher
echo ===================================================
echo   Mobile Phone Repair Status Tracking System
echo   Laravel 11.x + MySQL + Flutter Web Client
echo ===================================================
echo.

where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    if exist C:\xampp\php\php.exe (
        set "PATH=C:\xampp\php;%PATH%"
    ) else if exist D:\xampp\php\php.exe (
        set "PATH=D:\xampp\php;%PATH%"
    )
)

where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] PHP was not found in PATH or standard XAMPP folders.
    echo Please make sure XAMPP is installed or add PHP to your environment variables.
    pause
    exit /b 1
)

if not exist bootstrap\cache mkdir bootstrap\cache
if not exist storage\framework\cache mkdir storage\framework\cache
if not exist storage\framework\cache\data mkdir storage\framework\cache\data
if not exist storage\framework\sessions mkdir storage\framework\sessions
if not exist storage\framework\views mkdir storage\framework\views
if not exist storage\logs mkdir storage\logs

if not exist .env (
    echo [.env file not found. Copying from .env.example...]
    copy .env.example .env >nul
)

if not exist vendor\autoload.php (
    echo [INFO] Composer dependencies missing. Running composer install...
    call composer install --ignore-platform-reqs --no-interaction
    if not exist vendor\autoload.php (
        echo.
        echo ===================================================
        echo [ERROR] Composer install did not complete successfully.
        echo Common fixes:
        echo 1. Check your internet connection.
        echo 2. Enable 'extension=zip' and 'extension=fileinfo' in xampp/php/php.ini
        echo ===================================================
        echo.
        pause
        exit /b 1
    )
)

echo [1/4] Setting up MySQL Database (phone_repair_db)...
php setup_db.php
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ===================================================
    echo [ERROR] Database setup failed.
    echo Please make sure MySQL is started in XAMPP Control Panel.
    echo ===================================================
    echo.
    pause
    exit /b %ERRORLEVEL%
)

echo [2/4] Generating Application Key...
call php artisan key:generate --force
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Failed to generate application key.
    pause
    exit /b %ERRORLEVEL%
)

echo [3/4] Running Migrations and Seeders...
call php artisan migrate:fresh --seed --force
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Failed to run migrations and seeders.
    pause
    exit /b %ERRORLEVEL%
)

echo [4/4] Opening Web Portals in Default Browser...
start "" "http://localhost:8080/admin/dashboard"
start "" "http://localhost:8080/flutter"
start "" "http://localhost:8080/api-docs"

echo.
echo ===================================================
echo   Server is running at http://localhost:8080
echo   - Tab 1 (Admin Portal):   http://localhost:8080/admin/dashboard
echo   - Tab 2 (Flutter Web):    http://localhost:8080/flutter
echo   - Tab 3 (Raw JSON API):   http://localhost:8080/api-docs
echo.
echo   Press CTRL+C anytime to stop the server.
echo ===================================================
echo.
call php artisan serve --port=8080
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ===================================================
    echo [ERROR] Server stopped unexpectedly.
    echo Please check if port 8080 is already in use.
    echo ===================================================
    echo.
    pause
)
