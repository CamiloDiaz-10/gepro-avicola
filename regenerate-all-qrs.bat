@echo off
echo ============================================
echo   REGENERAR TODOS LOS CODIGOS QR
echo ============================================
echo.
echo ADVERTENCIA: Esto regenerara TODOS los QR
echo Los QR antiguos dejaran de funcionar
echo.
pause
echo.

php artisan birds:regenerate-all-qrs --force

echo.
echo ============================================
echo   Proceso completado
echo ============================================
echo.
echo Ahora puedes escanear los nuevos QR
echo o descargarlos desde el sistema
echo.
pause
