<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role->NombreRol;

        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'No tienes permiso para acceder a esta sección.');
    }
}