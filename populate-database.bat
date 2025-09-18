@echo off
echo ========================================
echo    GEPRO AVICOLA - POBLADO DE BD
echo ========================================
echo.

echo [1/4] Limpiando cache de configuracion...
php artisan config:clear

echo [2/4] Limpiando cache de rutas...
php artisan route:clear

echo [3/4] Ejecutando migraciones frescas...
php artisan migrate:fresh

echo [4/4] Poblando base de datos con seeders...
php artisan db:seed

echo.
echo ========================================
echo    BASE DE DATOS POBLADA EXITOSAMENTE
echo ========================================
echo.
echo Usuarios de prueba disponibles:
echo - admin@geproavicola.com (admin123)
echo - propietario@geproavicola.com (propietario123)
echo - empleado@geproavicola.com (empleado123)
echo.
echo Presiona cualquier tecla para continuar...
pause > nul
