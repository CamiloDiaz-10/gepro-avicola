@echo off
echo ========================================
echo   PRUEBA DE MODULO DE USUARIOS
echo ========================================
echo.

echo [1/5] Limpiando cache...
call php artisan config:clear
call php artisan route:clear
call php artisan view:clear
echo.

echo [2/5] Verificando migraciones...
call php artisan migrate:status
echo.

echo [3/5] Listando rutas de usuarios...
call php artisan route:list --name=admin.users
echo.

echo [4/5] Verificando archivos...
echo Verificando UserController...
if exist "app\Http\Controllers\Admin\UserController.php" (
    echo   [OK] UserController.php existe
) else (
    echo   [ERROR] UserController.php no encontrado
)

echo Verificando vistas...
if exist "resources\views\admin\users\index.blade.php" (
    echo   [OK] index.blade.php existe
) else (
    echo   [ERROR] index.blade.php no encontrado
)

if exist "resources\views\admin\users\show.blade.php" (
    echo   [OK] show.blade.php existe
) else (
    echo   [ERROR] show.blade.php no encontrado
)

if exist "resources\views\admin\users\edit.blade.php" (
    echo   [OK] edit.blade.php existe
) else (
    echo   [ERROR] edit.blade.php no encontrado
)

if exist "resources\views\admin\users\create.blade.php" (
    echo   [OK] create.blade.php existe
) else (
    echo   [ERROR] create.blade.php no encontrado
)
echo.

echo [5/5] Verificando modelo User...
if exist "app\Models\User.php" (
    echo   [OK] User.php existe
) else (
    echo   [ERROR] User.php no encontrado
)
echo.

echo ========================================
echo   VERIFICACION COMPLETADA
echo ========================================
echo.
echo Para probar el modulo:
echo 1. Inicia el servidor: php artisan serve
echo 2. Accede a: http://localhost:8000/login
echo 3. Inicia sesion como admin@geproavicola.com / admin123
echo 4. Ve a: http://localhost:8000/admin/users
echo.
echo Presiona cualquier tecla para salir...
pause > nul
