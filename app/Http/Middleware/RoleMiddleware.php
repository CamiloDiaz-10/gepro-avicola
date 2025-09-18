<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        try {
            // Verificar si el usuario tiene una relación de rol cargada
            if (!$user->role) {
                // Intentar cargar la relación si no está cargada
                $user->load('role');
            }
            
            $userRole = $user->role ? $user->role->NombreRol : null;

            if (!$userRole) {
                return redirect()->route('dashboard')->with('error', 'Tu cuenta no tiene un rol asignado. Contacta al administrador.');
            }

            if ($userRole !== $role) {
                return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta página.');
            }

            return $next($request);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('RoleMiddleware error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Error al verificar permisos. Intenta iniciar sesión nuevamente.');
        }
    }
}
