<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
// Controladores comentados hasta crearlos:
// use App\Http\Controllers\UserController;
// use App\Http\Controllers\RoleController;
// use App\Http\Controllers\SystemController;
// use App\Http\Controllers\FincaController;
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
    
    // Dashboards específicos por rol
    Route::get('/admin/dashboard', function () {
        return view('dashboard.admin', ['statistics' => app(\App\Services\DashboardService::class)->getStatistics()]);
    })->name('admin.dashboard')->middleware('role:Administrador');
    
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
    
    // Rutas específicas para administradores (comentadas hasta crear los controladores)
    /*
    Route::middleware(['role:Administrador'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/system/logs', [SystemController::class, 'logs'])->name('system.logs');
    });
    
    // Rutas específicas para propietarios (comentadas hasta crear los controladores)
    Route::middleware(['role:Propietario'])->group(function () {
        Route::resource('fincas', FincaController::class);
        Route::resource('birds', BirdController::class);
        
        // Rutas de reportes
        Route::prefix('reports')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/production', [ReportController::class, 'generateProductionReport'])->name('reports.production');
            Route::get('/inventory', [ReportController::class, 'generateInventoryReport'])->name('reports.inventory');
            Route::get('/health', [ReportController::class, 'generateHealthReport'])->name('reports.health');
        });
    });
    
    // Rutas específicas para empleados (comentadas hasta crear los controladores)
    Route::middleware(['role:Empleado'])->group(function () {
        Route::resource('egg-production', EggProductionController::class);
        Route::resource('health-records', HealthRecordController::class);
    });
    */
});
