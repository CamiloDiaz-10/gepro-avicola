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
        $base = [
            'users' => $this->getUserStatistics(),
            'birds' => $this->getBirdStatistics(),
            'eggProduction' => $this->getEggProductionStatistics(),
            'health' => $this->getHealthStatistics(),
            'inventory' => $this->getInventoryStatistics(),
        ];

        $user = auth()->user();
        $role = $user && $user->role ? $user->role->NombreRol : null;
        if ($role === 'Empleado') {
            $base = array_merge($base, $this->getEmployeeStatistics());
        }

        return $base;
    }

    public function getEmployeeStatistics(): array
    {
        $user = auth()->user();
        $assignedFarms = 0;
        $farms = collect();
        $eggsToday = 0;
        if ($user) {
            try {
                $farms = $user->fincas()->select('fincas.IDFinca','fincas.Nombre','fincas.Ubicacion')->orderBy('Nombre')->get();
                $assignedFarms = $farms->count();
                if (Schema::hasTable('produccion_huevos')) {
                    $fincaIds = $farms->pluck('IDFinca');
                    if ($fincaIds->isNotEmpty()) {
                        $eggsToday = DB::table('produccion_huevos as ph')
                            ->join('lotes as l','ph.IDLote','=','l.IDLote')
                            ->whereIn('l.IDFinca', $fincaIds)
                            ->whereDate('ph.Fecha', now()->toDateString())
                            ->sum('ph.CantidadHuevos');
                    }
                }
            } catch (\Throwable $e) {
                $assignedFarms = 0;
                $farms = collect();
            }
        }
        return [
            'assignedFarms' => $assignedFarms,
            'assignedFarmsList' => $farms,
            'eggsToday' => (int) $eggsToday,
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