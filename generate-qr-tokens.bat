@echo off
echo ============================================
echo   Generando Tokens QR para Aves
echo ============================================
echo.

REM Ejecutar la migración primero por si acaso
echo [1/2] Verificando migraciones...
php artisan migrate --force
echo.

REM Generar tokens QR
echo [2/2] Generando tokens QR para aves...
php artisan birds:generate-qr-tokens
echo.

echo ============================================
echo   Proceso completado
echo ============================================
echo.
pause
