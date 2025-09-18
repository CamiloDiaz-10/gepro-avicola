<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Finca;
use App\Services\DashboardService;

class HomeController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        return view('welcome');
    }

    public function dashboard()
    {
        $user = auth()->user();
        $role = $user->role ? $user->role->NombreRol : null;
        $statistics = $this->dashboardService->getStatistics();

        switch ($role) {
            case 'Administrador':
                return view('dashboard.admin', compact('statistics'));
            case 'Propietario':
                return view('dashboard.owner', compact('statistics'));
            case 'Empleado':
                return view('dashboard.employee', compact('statistics'));
            default:
                return view('dashboard', compact('statistics'));
        }
    }
}