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

        // Verificar si el usuario tiene fincas asignadas (excepto admin)
        if ($role !== 'Administrador' && $user && !$user->hasFincasAsignadas()) {
            return [
                'error' => 'sin_fincas',
                'message' => 'No tienes fincas asignadas'
            ];
        }

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

}