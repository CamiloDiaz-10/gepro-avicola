<?php

namespace App\Traits;

trait FiltroFincasHelper
{
    /**
     * Aplicar filtro de fincas basado en el usuario autenticado
     */
    protected function aplicarFiltroFincas($query, $user = null)
    {
        $user = $user ?? auth()->user();

        // Administradores ven todo
        if ($user && $user->hasRole('Administrador')) {
            return $query;
        }

        // Obtener IDs de fincas del usuario
        if ($user) {
            $fincaIds = $user->getFincaIds();
            
            if (empty($fincaIds)) {
                // Sin fincas asignadas, no mostrar nada
                return $query->whereRaw('1 = 0');
            }

            return $query->deUsuarioFincas($user->IDUsuario);
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * Obtener fincas accesibles para el usuario
     */
    protected function getFincasAccesibles($user = null)
    {
        $user = $user ?? auth()->user();

        // Administradores ven todas las fincas
        if ($user && $user->hasRole('Administrador')) {
            return \App\Models\Finca::orderBy('Nombre')->get();
        }

        // Usuarios normales solo sus fincas asignadas
        if ($user) {
            return $user->fincas()->orderBy('Nombre')->get();
        }

        return collect();
    }

    /**
     * Obtener lotes accesibles para el usuario
     */
    protected function getLotesAccesibles($user = null)
    {
        $user = $user ?? auth()->user();

        // Administradores ven todos los lotes
        if ($user && $user->hasRole('Administrador')) {
            return \App\Models\Lote::with('finca')->orderBy('Nombre')->get();
        }

        // Usuarios normales solo lotes de sus fincas
        if ($user) {
            $fincaIds = $user->getFincaIds();
            return \App\Models\Lote::with('finca')
                ->whereIn('IDFinca', $fincaIds)
                ->orderBy('Nombre')
                ->get();
        }

        return collect();
    }

    /**
     * Verificar si el usuario tiene acceso a una finca específica
     */
    protected function verificarAccesoFinca($fincaId, $user = null)
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return false;
        }

        return $user->hasAccessToFinca($fincaId);
    }

    /**
     * Verificar si el usuario tiene acceso a un lote específico
     */
    protected function verificarAccesoLote($loteId, $user = null)
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return false;
        }

        return $user->hasAccessToLote($loteId);
    }
}
