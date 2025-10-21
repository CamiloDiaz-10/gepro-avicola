<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Bird;
use App\Models\EggProduction;
use App\Models\Finca;

class DashboardService
{
    public function getStatistics()
    {
        $user = auth()->user();
        $role = $user && $user->role ? $user->role->NombreRol : null;

        // Nota: Si el usuario no tiene fincas asignadas, devolvemos estadísticas en cero
        // en lugar de bloquear el dashboard. Los módulos de escritura se protegen con middleware.

        $base = [
            'users' => $this->getUserStatistics(),
            'birds' => $this->getBirdStatistics(),
            'eggProduction' => $this->getEggProductionStatistics(),
            'health' => $this->getHealthStatistics(),
            'inventory' => $this->getInventoryStatistics(),
        ];

        // Agregar estadísticas específicas por rol
        if ($role === 'Empleado' || $role === 'Propietario') {
            $base = array_merge($base, $this->getAssignedFarmsStatistics());
        }
        if ($role === 'Veterinario') {
            $base = array_merge($base, $this->getVeterinarioStatistics());
        }

        return $base;
    }

    public function getEmployeeStatistics(): array
    {
        return $this->getAssignedFarmsStatistics();
    }

    public function getAssignedFarmsStatistics(): array
    {
        $user = auth()->user();
        $assignedFarms = 0;
        $farms = collect();
        $eggsToday = 0;
        $totalLots = 0;
        $totalBirds = 0;

        if ($user) {
            try {
                // Obtener fincas asignadas con información adicional
                $farms = $user->fincas()
                    ->select('fincas.IDFinca','fincas.Nombre','fincas.Ubicacion', 'fincas.Hectareas')
                    ->orderBy('Nombre')
                    ->get();
                
                $assignedFarms = $farms->count();
                $fincaIds = $farms->pluck('IDFinca');

                if ($fincaIds->isNotEmpty()) {
                    // Contar lotes de las fincas asignadas
                    if (Schema::hasTable('lotes')) {
                        $totalLots = DB::table('lotes')
                            ->whereIn('IDFinca', $fincaIds)
                            ->count();
                    }

                    // Contar aves de las fincas asignadas
                    if (Schema::hasTable('gallinas') && Schema::hasTable('lotes')) {
                        $totalBirds = DB::table('gallinas')
                            ->join('lotes', 'gallinas.IDLote', '=', 'lotes.IDLote')
                            ->whereIn('lotes.IDFinca', $fincaIds)
                            ->count();
                    }

                    // Producción de huevos hoy
                    if (Schema::hasTable('produccion_huevos')) {
                        $eggsToday = DB::table('produccion_huevos as ph')
                            ->join('lotes as l','ph.IDLote','=','l.IDLote')
                            ->whereIn('l.IDFinca', $fincaIds)
                            ->whereDate('ph.Fecha', now()->toDateString())
                            ->sum('ph.CantidadHuevos');
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('Error obteniendo estadísticas de fincas: ' . $e->getMessage());
                $assignedFarms = 0;
                $farms = collect();
            }
        }

        return [
            'farms' => [
                'total' => $assignedFarms,
                'list' => $farms,
            ],
            'lots' => [
                'total' => $totalLots,
            ],
            'birds' => [
                'total' => $totalBirds,
            ],
            'eggProduction' => [
                'today' => (int) $eggsToday,
            ],
            'assignedFarms' => $assignedFarms, // Backward compatibility
            'assignedFarmsList' => $farms, // Backward compatibility
            'eggsToday' => (int) $eggsToday, // Backward compatibility
            'todayTasks' => (int) $eggsToday,
            'pendingReports' => 0,
        ];
    }

    protected function getUserStatistics()
    {
        return [
            'total' => User::count(),
            'newUsers' => User::where('created_at', '>=', now()->subDays(7))->count()
        ];
    }

    protected function getBirdStatistics()
    {
        if (!Schema::hasTable('gallinas')) {
            return [
                'total' => 0,
                'byStatus' => [],
                'recentArrivals' => 0
            ];
        }

        return [
            'total' => Bird::getTotalBirdsCount(),
            'byStatus' => Bird::getBirdsByStatus(),
            'recentArrivals' => Bird::getRecentAcquisitions(7)
        ];
    }

    protected function getEggProductionStatistics()
    {
        if (!Schema::hasTable('produccion_huevos')) {
            return [
                'today' => 0,
                'week' => 0,
                'month' => 0,
                'qualityDistribution' => [],
                'weeklyProduction' => []
            ];
        }

        return [
            'today' => EggProduction::getTotalEggsToday(),
            'week' => EggProduction::whereBetween('Fecha', [now()->startOfWeek(), now()])->sum('CantidadHuevos') ?? 0,
            'month' => EggProduction::whereBetween('Fecha', [now()->startOfMonth(), now()])->sum('CantidadHuevos') ?? 0,
            'qualityDistribution' => EggProduction::getProductionByQuality(),
            'weeklyProduction' => EggProduction::getWeeklyProduction()
        ];
    }

    protected function getHealthStatistics()
    {
        return [
            'conditionsSummary' => [],
            'recentTreatments' => 0
        ];
    }

    protected function getInventoryStatistics()
    {
        return [
            'byType' => [],
            'lowStock' => []
        ];
    }

    /**
     * Estadísticas específicas para el dashboard del Veterinario
     * - feedingRecords: cantidad de registros de alimentación del mes actual en sus fincas
     * - activeLots: cantidad de lotes con aves activas en sus fincas
     * - monthlyConsumption: suma (Kg) de alimentación en el mes actual en sus fincas
     */
    protected function getVeterinarioStatistics(): array
    {
        $user = auth()->user();
        $feedingRecords = 0;
        $activeLots = 0;
        $monthlyConsumption = 0.0;

        if (!$user) {
            return compact('feedingRecords','activeLots','monthlyConsumption');
        }

        try {
            // Obtener fincas asignadas
            $fincaIds = $user->getFincaIds();
            if (empty($fincaIds)) {
                return compact('feedingRecords','activeLots','monthlyConsumption');
            }

            // Registros de alimentación del mes actual
            if (Schema::hasTable('alimentacion') && Schema::hasTable('lotes')) {
                $feedingRecords = DB::table('alimentacion as a')
                    ->join('lotes as l','a.IDLote','=','l.IDLote')
                    ->whereIn('l.IDFinca', $fincaIds)
                    ->whereBetween('a.Fecha', [now()->startOfMonth(), now()])
                    ->count();

                // Consumo mensual (Kg)
                $monthlyConsumption = (float) DB::table('alimentacion as a')
                    ->join('lotes as l','a.IDLote','=','l.IDLote')
                    ->whereIn('l.IDFinca', $fincaIds)
                    ->whereBetween('a.Fecha', [now()->startOfMonth(), now()])
                    ->sum('a.CantidadKg');
            }

            // Lotes activos (con al menos una ave activa)
            if (Schema::hasTable('lotes') && Schema::hasTable('gallinas')) {
                $activeLots = DB::table('lotes as l')
                    ->join('gallinas as g','g.IDLote','=','l.IDLote')
                    ->whereIn('l.IDFinca', $fincaIds)
                    ->where(function($q) {
                        $q->whereRaw("LOWER(TRIM(COALESCE(g.Estado,''))) = 'activa'")
                          ->orWhereRaw("LOWER(TRIM(COALESCE(g.Estado,''))) = 'activo'");
                    })
                    ->distinct('l.IDLote')
                    ->count('l.IDLote');
            }
        } catch (\Throwable $e) {
            \Log::error('Error obteniendo estadísticas de veterinario: ' . $e->getMessage());
        }

        return [
            'feedingRecords' => (int) $feedingRecords,
            'activeLots' => (int) $activeLots,
            'monthlyConsumption' => (float) $monthlyConsumption,
        ];
    }

}