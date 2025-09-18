<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        try {
            // Verificar si el usuario está autenticado
            if (!auth()->check()) {
                Log::warning('RoleMiddleware: Usuario no autenticado intentando acceder a ruta protegida');
                return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página.');
            }

            $user = auth()->user();
            
            // Verificar que el usuario existe
            if (!$user) {
                Log::error('RoleMiddleware: Usuario autenticado pero no encontrado en la base de datos');
                auth()->logout();
                return redirect()->route('login')->with('error', 'Sesión inválida. Por favor, inicia sesión nuevamente.');
            }

            // Recargar el usuario con la relación role para asegurar que esté actualizada
            $user = User::with('role')->find($user->getKey());
            
            if (!$user) {
                Log::error('RoleMiddleware: Usuario no encontrado al recargar desde la base de datos');
                auth()->logout();
                return redirect()->route('login')->with('error', 'Usuario no encontrado. Por favor, inicia sesión nuevamente.');
            }

            // Verificar que el usuario tiene un rol asignado
            if (!$user->role) {
                Log::warning("RoleMiddleware: Usuario {$user->Email} no tiene rol asignado");
                return redirect()->route('dashboard')->with('error', 'Tu cuenta no tiene un rol asignado. Contacta al administrador.');
            }

            $userRole = $user->role->NombreRol;

            // Verificar que el rol del usuario coincide con el requerido
            if ($userRole !== $role) {
                Log::warning("RoleMiddleware: Usuario {$user->Email} con rol '{$userRole}' intentó acceder a ruta que requiere rol '{$role}'");
                return redirect()->route('dashboard')->with('error', "No tienes permisos para acceder a esta página. Se requiere rol: {$role}");
            }

            Log::info("RoleMiddleware: Usuario {$user->Email} con rol '{$userRole}' accedió correctamente a ruta que requiere rol '{$role}'");
            return $next($request);

        } catch (\Exception $e) {
            Log::error('RoleMiddleware: Error inesperado - ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id(),
                'required_role' => $role,
                'request_url' => $request->url()
            ]);
            
            return redirect()->route('dashboard')->with('error', 'Error al verificar permisos. Intenta iniciar sesión nuevamente.');
        }
    }
}
