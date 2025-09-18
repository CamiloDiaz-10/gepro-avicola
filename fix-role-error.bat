@echo off
echo ========================================
echo   SOLUCIONANDO ERROR DE MIDDLEWARE ROLE
echo ========================================

echo.
echo 1. Limpiando todas las caches...
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo.
echo 2. Optimizando autoloader...
composer dump-autoload

echo.
echo 3. Ejecutando diagnostico...
php artisan diagnose:role-middleware

echo.
echo 4. Recreando caches optimizadas...
php artisan config:cache
php artisan route:cache

echo.
echo ========================================
echo   SOLUCION APLICADA
echo ========================================
echo.
echo Ahora intenta:
echo 1. Cerrar sesion completamente
echo 2. Iniciar sesion nuevamente
echo 3. Acceder al dashboard de admin
echo.
echo Si el problema persiste, revisa los logs en storage/logs/
echo.
pause
