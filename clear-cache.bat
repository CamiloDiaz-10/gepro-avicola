@echo off
echo Limpiando caches de Laravel...

echo.
echo 1. Limpiando cache de configuracion...
php artisan config:clear

echo.
echo 2. Limpiando cache de rutas...
php artisan route:clear

echo.
echo 3. Limpiando cache de vistas...
php artisan view:clear

echo.
echo 4. Limpiando cache de aplicacion...
php artisan cache:clear

echo.
echo 5. Optimizando autoloader...
composer dump-autoload

echo.
echo 6. Recreando cache de configuracion...
php artisan config:cache

echo.
echo 7. Recreando cache de rutas...
php artisan route:cache

echo.
echo ¡Cache limpiado exitosamente!
echo Ahora intenta acceder nuevamente al dashboard de admin.
pause
