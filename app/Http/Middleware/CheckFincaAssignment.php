<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFincaAssignment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Los administradores siempre tienen acceso
        if ($user && $user->hasRole('Administrador')) {
            return $next($request);
        }

        // Verificar si el usuario tiene fincas asignadas
        if ($user && !$user->hasFincasAsignadas()) {
            return redirect()->route('sin-fincas')
                ->with('error', 'No tienes fincas asignadas. Contacta al administrador para obtener acceso.');
        }

        return $next($request);
    }
}
