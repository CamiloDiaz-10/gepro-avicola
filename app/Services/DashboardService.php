<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Bird;
use App\Models\EggProduction;
use App\Models\Finca;

class DashboardService
{
    public function getStatistics()
    {
        return [
            'users' => $this->getUserStatistics(),
            'birds' => $this->getBirdStatistics(),
            'eggProduction' => $this->getEggProductionStatistics(),
            'health' => $this->getHealthStatistics(),
            'inventory' => $this->getInventoryStatistics(),
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