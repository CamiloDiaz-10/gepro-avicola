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

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
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
    });
    
    // Rutas específicas para empleados 
    Route::middleware(['role:Empleado'])->prefix('employee')->name('employee.')->group(function () {
        // Producción de Huevos (Empleado)
        Route::get('produccion-huevos', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'index'])->name('produccion-huevos.index');
        Route::get('produccion-huevos/create', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'create'])->name('produccion-huevos.create');
        Route::post('produccion-huevos', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'store'])->name('produccion-huevos.store');
        Route::get('produccion-huevos/export/csv', [\App\Http\Controllers\Admin\ProduccionHuevosController::class, 'exportCsv'])->name('produccion-huevos.export.csv');
    });
});
