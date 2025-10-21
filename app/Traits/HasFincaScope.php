<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasFincaScope
{
    /**
     * Scope a query to only include records from user's assigned fincas
     */
    public function scopeDeUsuarioFincas(Builder $query, $userId)
    {
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return $query->whereRaw('1 = 0'); // No results
        }

        // Admins can see everything
        if ($user->hasRole('Administrador')) {
            return $query;
        }

        $fincaIds = $user->fincas()->pluck('fincas.IDFinca')->toArray();
        
        if (empty($fincaIds)) {
            return $query->whereRaw('1 = 0'); // No results if no fincas assigned
        }

        // Check if this model has IDFinca directly
        if (in_array('IDFinca', $this->getFillable())) {
            return $query->whereIn('IDFinca', $fincaIds);
        }

        // Check if this model has IDLote (needs to join through lotes)
        if (in_array('IDLote', $this->getFillable())) {
            return $query->whereHas('lote', function($q) use ($fincaIds) {
                $q->whereIn('IDFinca', $fincaIds);
            });
        }

        return $query;
    }

    /**
     * Scope to filter by specific finca IDs
     */
    public function scopeDeFincas(Builder $query, array $fincaIds)
    {
        if (in_array('IDFinca', $this->getFillable())) {
            return $query->whereIn('IDFinca', $fincaIds);
        }

        if (in_array('IDLote', $this->getFillable())) {
            return $query->whereHas('lote', function($q) use ($fincaIds) {
                $q->whereIn('IDFinca', $fincaIds);
            });
        }

        return $query;
    }
}
