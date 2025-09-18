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

        // Redirigir a los dashboards específicos según el rol
        switch ($role) {
            case 'Administrador':
                return redirect()->route('admin.dashboard');
            case 'Propietario':
                return redirect()->route('owner.dashboard');
            case 'Empleado':
                return redirect()->route('employee.dashboard');
            default:
                // Para usuarios sin rol específico, mostrar dashboard básico
                $statistics = $this->dashboardService->getStatistics();
                return view('dashboard', compact('statistics'));
        }
    }
}