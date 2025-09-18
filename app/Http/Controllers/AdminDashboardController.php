<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Role;
use App\Models\Finca;

class AdminDashboardController extends Controller
{
    // El middleware se aplica en las rutas

    public function index()
    {
        $statistics = $this->getAdminStatistics();
        
        return view('admin.dashboard', compact('statistics'));
    }

    private function getAdminStatistics()
    {
        return [
            'overview' => $this->getOverviewStats(),
            'users' => $this->getUserStats(),
            'farms' => $this->getFarmStats(),
            'birds' => $this->getBirdStats(),
            'production' => $this->getProductionStats(),
            'health' => $this->getHealthStats(),
            'feeding' => $this->getFeedingStats(),
            'financial' => $this->getFinancialStats(),
            'recent_activities' => $this->getRecentActivities()
        ];
    }

    private function getOverviewStats()
    {
        return [
            'total_users' => $this->safeCount('usuarios'),
            'total_farms' => $this->safeCount('fincas'),
            'total_lots' => $this->safeCount('lotes'),
            'total_birds' => $this->safeCount('gallinas'),
            'active_lots' => $this->safeCount('lotes'), // Todos los lotes por ahora
            'today_production' => $this->getTodayEggProduction(),
            'pending_health_alerts' => $this->getPendingHealthAlerts(),
            'low_stock_feeds' => $this->getLowStockFeeds()
        ];
    }

    private function getUserStats()
    {
        if (!Schema::hasTable('usuarios')) {
            return ['by_role' => [], 'recent_registrations' => [], 'active_users' => 0];
        }

        return [
            'by_role' => DB::table('usuarios')
                ->join('roles', 'usuarios.IDRol', '=', 'roles.IDRol')
                ->select('roles.NombreRol as role', DB::raw('count(*) as total'))
                ->groupBy('roles.NombreRol')
                ->get(),
            'recent_registrations' => DB::table('usuarios')
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['Nombre', 'Apellido', 'Email', 'created_at']),
            'active_users' => DB::table('usuarios')
                ->where('updated_at', '>=', now()->subDays(30))
                ->count()
        ];
    }

    private function getFarmStats()
    {
        if (!Schema::hasTable('fincas')) {
            return ['total' => 0, 'by_location' => [], 'with_lots' => 0];
        }

        return [
            'total' => DB::table('fincas')->count(),
            'by_location' => DB::table('fincas')
                ->select('Ubicacion', DB::raw('count(*) as total'))
                ->groupBy('Ubicacion')
                ->get(),
            'with_lots' => DB::table('fincas')
                ->join('lotes', 'fincas.IDFinca', '=', 'lotes.IDFinca')
                ->distinct('fincas.IDFinca')
                ->count()
        ];
    }

    private function getBirdStats()
    {
        if (!Schema::hasTable('gallinas')) {
            return ['by_type' => [], 'by_status' => [], 'by_age_group' => []];
        }

        return [
            'by_type' => DB::table('gallinas')
                ->join('tipo_gallinas', 'gallinas.IDTipoGallina', '=', 'tipo_gallinas.IDTipoGallina')
                ->select('tipo_gallinas.Nombre as type', DB::raw('count(*) as total'))
                ->groupBy('tipo_gallinas.Nombre')
                ->get(),
            'by_status' => DB::table('gallinas')
                ->select('Estado as status', DB::raw('count(*) as total'))
                ->groupBy('Estado')
                ->get(),
            'by_age_group' => $this->getBirdsByAgeGroup()
        ];
    }

    private function getProductionStats()
    {
        if (!Schema::hasTable('produccion_huevos')) {
            return ['daily' => [], 'weekly' => [], 'monthly' => [], 'by_quality' => []];
        }

        return [
            'daily' => DB::table('produccion_huevos')
                ->select('Fecha as date', DB::raw('SUM(CantidadHuevos) as total'))
                ->where('Fecha', '>=', now()->subDays(7))
                ->groupBy('Fecha')
                ->orderBy('Fecha')
                ->get(),
            'weekly' => DB::table('produccion_huevos')
                ->select(DB::raw('WEEK(Fecha) as week'), DB::raw('SUM(CantidadHuevos) as total'))
                ->where('Fecha', '>=', now()->subWeeks(4))
                ->groupBy(DB::raw('WEEK(Fecha)'))
                ->get(),
            'monthly' => DB::table('produccion_huevos')
                ->select(DB::raw('MONTH(Fecha) as month'), DB::raw('SUM(CantidadHuevos) as total'))
                ->where('Fecha', '>=', now()->subMonths(6))
                ->groupBy(DB::raw('MONTH(Fecha)'))
                ->get(),
            'by_quality' => DB::table('produccion_huevos')
                ->select('Turno as quality', DB::raw('SUM(CantidadHuevos) as total'))
                ->where('Fecha', '>=', now()->subDays(30))
                ->groupBy('Turno')
                ->get()
        ];
    }

    private function getHealthStats()
    {
        if (!Schema::hasTable('sanidad')) {
            return ['treatments' => [], 'vaccinations' => [], 'mortality' => []];
        }

        return [
            'treatments' => DB::table('sanidad')
                ->select('TipoTratamiento as treatment', DB::raw('count(*) as total'))
                ->where('Fecha', '>=', now()->subDays(30))
                ->groupBy('TipoTratamiento')
                ->get(),
            'vaccinations' => DB::table('sanidad')
                ->where('TipoTratamiento', 'Vacuna')
                ->where('Fecha', '>=', now()->subDays(30))
                ->count(),
            'mortality' => $this->getMortalityStats()
        ];
    }

    private function getFeedingStats()
    {
        if (!Schema::hasTable('alimentacion')) {
            return ['consumption' => [], 'by_feed_type' => [], 'costs' => []];
        }

        return [
            'consumption' => DB::table('alimentacion')
                ->select('Fecha as date', DB::raw('SUM(CantidadKg) as total'))
                ->where('Fecha', '>=', now()->subDays(7))
                ->groupBy('Fecha')
                ->orderBy('Fecha')
                ->get(),
            'by_feed_type' => DB::table('alimentacion')
                ->join('tipo_alimentos', 'alimentacion.IDTipoAlimento', '=', 'tipo_alimentos.IDTipoAlimento')
                ->select('tipo_alimentos.Nombre as feed_type', DB::raw('SUM(CantidadKg) as total'))
                ->where('alimentacion.Fecha', '>=', now()->subDays(30))
                ->groupBy('tipo_alimentos.Nombre')
                ->get(),
            'costs' => (object)['total_cost' => 0] // No hay columna PrecioPorKg en tipo_alimentos
        ];
    }

    private function getFinancialStats()
    {
        if (!Schema::hasTable('movimiento_lote')) {
            return ['sales' => [], 'purchases' => [], 'revenue' => 0];
        }

        return [
            'sales' => DB::table('movimiento_lote')
                ->where('TipoMovimiento', 'Venta')
                ->where('Fecha', '>=', now()->subDays(30))
                ->count(), // Contar ventas ya que no hay ValorTotal
            'purchases' => DB::table('movimiento_lote')
                ->where('TipoMovimiento', 'Compra')
                ->where('Fecha', '>=', now()->subDays(30))
                ->count(), // Contar compras ya que no hay ValorTotal
            'revenue' => $this->calculateRevenue()
        ];
    }

    private function getRecentActivities()
    {
        $activities = [];

        // Recent user registrations
        if (Schema::hasTable('usuarios')) {
            $recentUsers = DB::table('usuarios')
                ->select('Nombre', 'Apellido', 'created_at', DB::raw("'user_registration' as type"))
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            $activities = array_merge($activities, $recentUsers->toArray());
        }

        // Recent lot movements
        if (Schema::hasTable('movimiento_lote')) {
            $recentMovements = DB::table('movimiento_lote')
                ->join('lotes', 'movimiento_lote.IDLote', '=', 'lotes.IDLote')
                ->select('lotes.Nombre', 'movimiento_lote.TipoMovimiento', 'movimiento_lote.Fecha as created_at', DB::raw("'lot_movement' as type"))
                ->where('movimiento_lote.Fecha', '>=', now()->subDays(7))
                ->orderBy('movimiento_lote.Fecha', 'desc')
                ->limit(5)
                ->get();
            $activities = array_merge($activities, $recentMovements->toArray());
        }

        // Sort by date
        usort($activities, function($a, $b) {
            return strtotime($b->created_at) - strtotime($a->created_at);
        });

        return array_slice($activities, 0, 10);
    }

    // Helper methods
    private function safeCount($table)
    {
        return Schema::hasTable($table) ? DB::table($table)->count() : 0;
    }

    private function safeQuery($table, $condition)
    {
        return Schema::hasTable($table) ? DB::table($table)->whereRaw($condition)->count() : 0;
    }

    private function getTodayEggProduction()
    {
        if (!Schema::hasTable('produccion_huevos')) {
            return 0;
        }

        return DB::table('produccion_huevos')
            ->where('Fecha', now()->toDateString())
            ->sum('CantidadHuevos') ?? 0;
    }

    private function getPendingHealthAlerts()
    {
        if (!Schema::hasTable('sanidad')) {
            return 0;
        }

        // Contar tratamientos recientes como indicador de actividad sanitaria
        return DB::table('sanidad')
            ->where('Fecha', '>=', now()->subDays(30))
            ->count();
    }

    private function getLowStockFeeds()
    {
        if (!Schema::hasTable('tipo_alimentos')) {
            return 0;
        }

        // Por ahora retornamos 0 ya que no hay columna de stock en la tabla
        // Se puede implementar cuando se agregue gestión de inventario
        return 0;
    }

    private function getBirdsByAgeGroup()
    {
        if (!Schema::hasTable('gallinas')) {
            return [];
        }

        return DB::table('gallinas')
            ->select(
                DB::raw('CASE 
                    WHEN DATEDIFF(NOW(), FechaNacimiento) < 90 THEN "Pollitos (0-3 meses)"
                    WHEN DATEDIFF(NOW(), FechaNacimiento) < 180 THEN "Jóvenes (3-6 meses)"
                    WHEN DATEDIFF(NOW(), FechaNacimiento) < 365 THEN "Adultos (6-12 meses)"
                    ELSE "Veteranos (+12 meses)"
                END as age_group'),
                DB::raw('count(*) as total')
            )
            ->whereNotNull('FechaNacimiento')
            ->groupBy(DB::raw('CASE 
                WHEN DATEDIFF(NOW(), FechaNacimiento) < 90 THEN "Pollitos (0-3 meses)"
                WHEN DATEDIFF(NOW(), FechaNacimiento) < 180 THEN "Jóvenes (3-6 meses)"
                WHEN DATEDIFF(NOW(), FechaNacimiento) < 365 THEN "Adultos (6-12 meses)"
                ELSE "Veteranos (+12 meses)"
            END'))
            ->get();
    }

    private function getMortalityStats()
    {
        if (!Schema::hasTable('mortalidad')) {
            return ['total_month' => 0, 'by_cause' => []];
        }

        return [
            'total_month' => DB::table('mortalidad')
                ->where('Fecha', '>=', now()->subDays(30))
                ->sum('Cantidad'),
            'by_cause' => DB::table('mortalidad')
                ->select('Causa as cause', DB::raw('SUM(Cantidad) as total'))
                ->where('Fecha', '>=', now()->subDays(30))
                ->groupBy('Causa')
                ->get()
        ];
    }

    private function calculateRevenue()
    {
        $eggRevenue = 0;
        $birdRevenue = 0;

        // Calculate egg revenue (assuming average price per egg)
        if (Schema::hasTable('produccion_huevos')) {
            $totalEggs = DB::table('produccion_huevos')
                ->where('Fecha', '>=', now()->subDays(30))
                ->sum('CantidadHuevos');
            $eggRevenue = $totalEggs * 500; // Assuming 500 pesos per egg
        }

        // Calculate bird sales revenue (count since no ValorTotal column)
        if (Schema::hasTable('movimiento_lote')) {
            $birdRevenue = DB::table('movimiento_lote')
                ->where('TipoMovimiento', 'Venta')
                ->where('Fecha', '>=', now()->subDays(30))
                ->count() * 50000; // Estimación: 50,000 pesos por ave vendida
        }

        return $eggRevenue + $birdRevenue;
    }
}
