<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class DiagnoseRoleMiddleware extends Command
{
    protected $signature = 'diagnose:role-middleware';
    protected $description = 'Diagnostica problemas con el middleware de roles';

    public function handle()
    {
        $this->info('🔍 Diagnosticando middleware de roles...');
        $this->newLine();

        // 1. Verificar que el middleware esté registrado
        $this->info('1. Verificando registro del middleware...');
        $kernel = app(\App\Http\Kernel::class);
        $middlewareAliases = $kernel->getMiddlewareAliases();
        
        if (isset($middlewareAliases['role'])) {
            $this->info('✅ Middleware "role" está registrado: ' . $middlewareAliases['role']);
        } else {
            $this->error('❌ Middleware "role" NO está registrado');
            return 1;
        }

        // 2. Verificar que la clase RoleMiddleware existe
        $this->info('2. Verificando clase RoleMiddleware...');
        if (class_exists(\App\Http\Middleware\RoleMiddleware::class)) {
            $this->info('✅ Clase RoleMiddleware existe');
        } else {
            $this->error('❌ Clase RoleMiddleware NO existe');
            return 1;
        }

        // 3. Verificar modelos
        $this->info('3. Verificando modelos...');
        if (class_exists(\App\Models\User::class)) {
            $this->info('✅ Modelo User existe');
        } else {
            $this->error('❌ Modelo User NO existe');
        }

        if (class_exists(\App\Models\Role::class)) {
            $this->info('✅ Modelo Role existe');
        } else {
            $this->error('❌ Modelo Role NO existe');
        }

        // 4. Verificar conexión a base de datos y datos
        $this->info('4. Verificando base de datos...');
        try {
            $userCount = User::count();
            $roleCount = Role::count();
            
            $this->info("✅ Usuarios en BD: {$userCount}");
            $this->info("✅ Roles en BD: {$roleCount}");

            // Mostrar roles disponibles
            $roles = Role::all();
            $this->info('Roles disponibles:');
            foreach ($roles as $role) {
                $this->line("  - {$role->NombreRol} (ID: {$role->IDRol})");
            }

        } catch (\Exception $e) {
            $this->error('❌ Error conectando a la base de datos: ' . $e->getMessage());
            return 1;
        }

        // 5. Verificar usuarios con roles
        $this->info('5. Verificando usuarios con roles...');
        try {
            $usersWithRoles = User::with('role')->get();
            $usersWithoutRoles = $usersWithRoles->filter(function($user) {
                return !$user->role;
            });

            $this->info("✅ Usuarios con rol asignado: " . ($usersWithRoles->count() - $usersWithoutRoles->count()));
            
            if ($usersWithoutRoles->count() > 0) {
                $this->warn("⚠️ Usuarios SIN rol asignado: " . $usersWithoutRoles->count());
                foreach ($usersWithoutRoles as $user) {
                    $this->line("  - {$user->Email} (IDRol: {$user->IDRol})");
                }
            }

        } catch (\Exception $e) {
            $this->error('❌ Error verificando usuarios: ' . $e->getMessage());
        }

        // 6. Verificar rutas protegidas
        $this->info('6. Verificando rutas protegidas...');
        $routes = \Route::getRoutes();
        $protectedRoutes = [];
        
        foreach ($routes as $route) {
            $middleware = $route->gatherMiddleware();
            foreach ($middleware as $m) {
                if (strpos($m, 'role:') === 0) {
                    $protectedRoutes[] = [
                        'uri' => $route->uri(),
                        'name' => $route->getName(),
                        'middleware' => $m
                    ];
                }
            }
        }

        if (count($protectedRoutes) > 0) {
            $this->info('✅ Rutas protegidas por rol:');
            foreach ($protectedRoutes as $route) {
                $this->line("  - {$route['uri']} ({$route['name']}) -> {$route['middleware']}");
            }
        } else {
            $this->warn('⚠️ No se encontraron rutas protegidas por rol');
        }

        $this->newLine();
        $this->info('🎉 Diagnóstico completado');
        
        return 0;
    }
}
