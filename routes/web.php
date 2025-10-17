<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FincaController;
use App\Http\Controllers\Admin\LoteController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SanidadController;
use App\Http\Controllers\Admin\BirdsController;
// Controladores comentados hasta crearlos:
// use App\Http\Controllers\UserController;
// use App\Http\Controllers\RoleController;
// use App\Http\Controllers\SystemController;
// use App\Http\Controllers\BirdController;
// use App\Http\Controllers\EggProductionController;
// use App\Http\Controllers\HealthRecordController;
// use App\Http\Controllers\ReportController;

// Ruta de inicio
Route::get('/', [HomeController::class, 'index'])->name('home');

// Ruta de bienvenida (alias para home)
Route::get('/welcome', [HomeController::class, 'index'])->name('welcome');

// Página offline (pública)
Route::view('/offline', 'offline')->name('offline');

// Vista de QR de aves (PÚBLICA) para permitir escaneo sin autenticación
Route::get('/admin/aves/qr/{token}', [BirdsController::class, 'showByQr'])->name('admin.aves.show.byqr');

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register']);

// Ruta de prueba para verificar autenticación
Route::get('/test-auth', function() {
    if (auth()->check()) {
        return response()->json([
            'authenticated' => true,
            'user' => auth()->user()->Email,
            'role' => auth()->user()->role->NombreRol ?? 'Sin rol'
        ]);
    }
    return response()->json(['authenticated' => false]);
})->name('test.auth');

// Ruta de login manual para testing
Route::get('/test-login', function() {
    $user = \App\Models\User::with('role')->where('Email', 'admin@geproavicola.com')->first();
    if ($user) {
        auth()->login($user, false);
        $rol = $user->role ? $user->role->NombreRol : null;
        
        $redirectRoute = match ($rol) {
            'Administrador' => 'admin.dashboard',
            'Propietario' => 'owner.dashboard',
            'Empleado' => 'employee.dashboard',
            default => 'dashboard'
        };
        
        return redirect()->route($redirectRoute);
    }
    return 'Usuario no encontrado';
});

// Rutas protegidas
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard general
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    
    // Ruta de ejemplo para demostrar la sincronización navbar-sidebar
    Route::get('/example-sidebar', function () {
        return view('example-with-sidebar');
    })->name('example.sidebar');
    
    // Dashboards específicos por rol
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard')->middleware('role:Administrador');
    
    Route::get('/owner/dashboard', function () {
        return view('dashboard.owner', ['statistics' => app(\App\Services\DashboardService::class)->getStatistics()]);
    })->name('owner.dashboard')->middleware('role:Propietario');
    
    Route::get('/employee/dashboard', function () {
        return view('dashboard.employee', ['statistics' => app(\App\Services\DashboardService::class)->getStatistics()]);
    })->name('employee.dashboard')->middleware('role:Empleado');
    
    // Rutas de perfil de usuario
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('/settings', [ProfileController::class, 'settings'])->name('profile.settings');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });
    
    // Rutas específicas para administradores
    Route::middleware(['role:Administrador'])->prefix('admin')->name('admin.')->group(function () {
        // Gestión de usuarios
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        
        // Gestión de fincas
        Route::resource('fincas', FincaController::class);
        // Gestión de lotes
        Route::resource('lotes', LoteController::class);
        // Reportes avanzados
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export/production', [ReportController::class, 'exportProduction'])->name('reports.export.production');
        Route::get('reports/export/feeding', [ReportController::class, 'exportFeeding'])->name('reports.export.feeding');
        Route::get('reports/export/health', [ReportController::class, 'exportHealth'])->name('reports.export.health');
        Route::get('reports/export/finance', [ReportController::class, 'exportFinance'])->name('reports.export.finance');
        
        // Gestión de tratamientos (sanidad)
        Route::resource('sanidad', SanidadController::class);
        
        // Producción de Huevos
        Route::get('produccion-huevos', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'index'])->name('produccion-huevos.index');
        Route::get('produccion-huevos/create', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'create'])->name('produccion-huevos.create');
        Route::post('produccion-huevos', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'store'])->name('produccion-huevos.store');
        Route::get('produccion-huevos/export/csv', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'exportCsv'])->name('produccion-huevos.export.csv');
        Route::delete('produccion-huevos/{id}', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'destroy'])->name('produccion-huevos.destroy');

        // Gestión de Aves
        Route::get('aves', [\App\Http\Controllers\Admin\BirdsController::class, 'index'])->name('aves.index');
        Route::get('aves/create', [\App\Http\Controllers\Admin\BirdsController::class, 'create'])->name('aves.create');
        Route::post('aves', [\App\Http\Controllers\Admin\BirdsController::class, 'store'])->name('aves.store');
        Route::patch('aves/{bird}/estado', [\App\Http\Controllers\Admin\BirdsController::class, 'updateEstado'])->name('aves.estado.update');
        Route::get('aves/export/csv', [\App\Http\Controllers\Admin\BirdsController::class, 'exportCsv'])->name('aves.export.csv');
        Route::get('aves/scan', [\App\Http\Controllers\Admin\BirdsController::class, 'scan'])->name('aves.scan');
        Route::post('aves/qr/{token}/regenerate', [\App\Http\Controllers\Admin\BirdsController::class, 'regenerateByQr'])->name('aves.qr.regenerate');
        Route::delete('aves/{bird}', [\App\Http\Controllers\Admin\BirdsController::class, 'destroy'])->name('aves.destroy');

        // Gestión de Alimentación
        Route::get('alimentacion', [\App\Http\Controllers\Admin\AlimentacionController::class, 'index'])->name('alimentacion.index');
        Route::get('alimentacion/create', [\App\Http\Controllers\Admin\AlimentacionController::class, 'create'])->name('alimentacion.create');
        Route::post('alimentacion', [\App\Http\Controllers\Admin\AlimentacionController::class, 'store'])->name('alimentacion.store');
        Route::get('alimentacion/export/csv', [\App\Http\Controllers\Admin\AlimentacionController::class, 'exportCsv'])->name('alimentacion.export.csv');
        
        // Otras rutas administrativas (comentadas hasta crear los controladores)
        /*
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/system/logs', [SystemController::class, 'logs'])->name('system.logs');
        Route::resource('lotes', LoteController::class);
        Route::resource('gallinas', GallinaController::class);
        */
    });
    
    // Rutas específicas para propietarios (comentadas hasta crear los controladores)
    Route::middleware(['role:Propietario'])->prefix('owner')->name('owner.')->group(function () {
        // Producción de Huevos (Propietario)
        Route::get('produccion-huevos', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'index'])->name('produccion-huevos.index');
        Route::get('produccion-huevos/create', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'create'])->name('produccion-huevos.create');
        Route::post('produccion-huevos', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'store'])->name('produccion-huevos.store');
        Route::get('produccion-huevos/export/csv', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'exportCsv'])->name('produccion-huevos.export.csv');
        Route::delete('produccion-huevos/{id}', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'destroy'])->name('produccion-huevos.destroy');

        // Aves (Propietario) reutilizando controlador Admin
        Route::get('aves', [\App\Http\Controllers\Admin\BirdsController::class, 'index'])->name('aves.index');
        Route::get('aves/create', [\App\Http\Controllers\Admin\BirdsController::class, 'create'])->name('aves.create');
        Route::post('aves', [\App\Http\Controllers\Admin\BirdsController::class, 'store'])->name('aves.store');
        Route::patch('aves/{bird}/estado', [\App\Http\Controllers\Admin\BirdsController::class, 'updateEstado'])->name('aves.estado.update');
        Route::get('aves/export/csv', [\App\Http\Controllers\Admin\BirdsController::class, 'exportCsv'])->name('aves.export.csv');
        Route::get('aves/scan', [\App\Http\Controllers\Admin\BirdsController::class, 'scan'])->name('aves.scan');
        Route::delete('aves/{bird}', [\App\Http\Controllers\Admin\BirdsController::class, 'destroy'])->name('aves.destroy');

        // Reportes (Propietario) reutilizando controlador Admin
        Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export/production', [\App\Http\Controllers\Admin\ReportController::class, 'exportProduction'])->name('reports.export.production');
        Route::get('reports/export/feeding', [\App\Http\Controllers\Admin\ReportController::class, 'exportFeeding'])->name('reports.export.feeding');
        Route::get('reports/export/health', [\App\Http\Controllers\Admin\ReportController::class, 'exportHealth'])->name('reports.export.health');
        Route::get('reports/export/finance', [\App\Http\Controllers\Admin\ReportController::class, 'exportFinance'])->name('reports.export.finance');
    });
    
    // Rutas específicas para empleados 
    Route::middleware(['role:Empleado'])->prefix('employee')->name('employee.')->group(function () {
        // Producción de Huevos (Empleado)
        Route::get('produccion-huevos', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'index'])->name('produccion-huevos.index');
        Route::get('produccion-huevos/create', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'create'])->name('produccion-huevos.create');
        Route::post('produccion-huevos', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'store'])->name('produccion-huevos.store');
        Route::get('produccion-huevos/export/csv', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'exportCsv'])->name('produccion-huevos.export.csv');

        // Fincas asignadas (Empleado) reutilizando controlador Admin
        Route::get('fincas', [\App\Http\Controllers\Admin\FincaController::class, 'index'])->name('fincas.index');
        Route::get('fincas/{finca}', [\App\Http\Controllers\Admin\FincaController::class, 'show'])->name('fincas.show');
    });
});
