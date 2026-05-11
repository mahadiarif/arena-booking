@echo off
echo ArenaBook — Database Setup Script
echo ==================================

echo [1] Checking MySQL connection...
"C:\xampp\mysql\bin\mysqladmin" -u root ping
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: MySQL is not running! Start it from XAMPP Control Panel first.
    pause
    exit /b 1
)

echo [2] Creating database...
"C:\xampp\mysql\bin\mysql" -u root -e "CREATE DATABASE IF NOT EXISTS arenabook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo [3] Running migrations...
php artisan migrate:fresh --force

echo [4] Running seeders...
php artisan db:seed --force

echo [5] Creating storage link...
php artisan storage:link

echo [6] Clearing cache...
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo.
echo ========================================
echo   ArenaBook setup complete!
echo   URL: http://localhost/arenabook/public
echo   Login: admin@arenabook.com / password
echo ========================================
pause
