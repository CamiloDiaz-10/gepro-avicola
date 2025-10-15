<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProduccionHuevosRequest;
use App\Models\Lote;
use App\Models\ProduccionHuevos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProduccionHuevosController extends Controller
{
    private function isEmployeeContext(Request $request): bool
    {
        $user = $request->user();
        return ($request->routeIs('employee.*')) || ($user && $user->role && $user->role->NombreRol === 'Empleado');
    }

    private function permittedLotIds(Request $request)
    {
        $user = $request->user();
        if (!$user) return collect();

        // Lotes pertenecientes a las fincas asignadas al usuario (empleado)
        $fincaIds = $user->fincas()->pluck('fincas.IDFinca');
        if ($fincaIds->isEmpty()) return collect();

        return Lote::whereIn('IDFinca', $fincaIds)->pluck('IDLote');
    }
    public function index(Request $request)
    {
        // Filtros
        $from = $request->input('from', Carbon::now()->subDays(7)->toDateString());
        $to = $request->input('to', Carbon::now()->toDateString());
        $loteId = $request->input('lote');
        $turno = $request->input('turno');

        $query = ProduccionHuevos::with(['lote'])
            ->whereBetween('Fecha', [$from, $to]);

        if ($this->isEmployeeContext($request)) {
            $allowedLotIds = $this->permittedLotIds($request);
            $query->whereIn('IDLote', $allowedLotIds);
        }

        if ($loteId) {
            $query->where('IDLote', $loteId);
        }
        if ($turno) {
            $query->where('Turno', $turno);
        }

        $producciones = $query->orderByDesc('Fecha')->paginate(15)->withQueryString();

        // Totales y estadísticas
        $totales = ProduccionHuevos::select(
            DB::raw('SUM(CantidadHuevos) as total_huevos'),
            DB::raw('SUM(HuevosRotos) as total_rotos')
        )->whereBetween('Fecha', [$from, $to])
         ->when($this->isEmployeeContext($request), function($q) use ($request) {
             $q->whereIn('IDLote', $this->permittedLotIds($request));
         })
         ->when($loteId, fn($q) => $q->where('IDLote', $loteId))
         ->when($turno, fn($q) => $q->where('Turno', $turno))
         ->first();

        $porcentajeRotos = 0;
        if (($totales->total_huevos ?? 0) > 0) {
            $porcentajeRotos = round(($totales->total_rotos ?? 0) * 100 / ($totales->total_huevos ?? 1), 2);
        }

        // Producción diaria para gráfico
        $serieDiaria = ProduccionHuevos::select('Fecha', DB::raw('SUM(CantidadHuevos) as total'))
            ->whereBetween('Fecha', [$from, $to])
            ->when($this->isEmployeeContext($request), function($q) use ($request) {
                $q->whereIn('IDLote', $this->permittedLotIds($request));
            })
            ->when($loteId, fn($q) => $q->where('IDLote', $loteId))
            ->when($turno, fn($q) => $q->where('Turno', $turno))
            ->groupBy('Fecha')
            ->orderBy('Fecha')
            ->get();

        // Mejores y peores días
        $mejoresDias = ProduccionHuevos::select('Fecha', DB::raw('SUM(CantidadHuevos) as total'))
            ->whereBetween('Fecha', [$from, $to])
            ->when($this->isEmployeeContext($request), function($q) use ($request) {
                $q->whereIn('IDLote', $this->permittedLotIds($request));
            })
            ->when($loteId, fn($q) => $q->where('IDLote', $loteId))
            ->when($turno, fn($q) => $q->where('Turno', $turno))
            ->groupBy('Fecha')
            ->orderByDesc('total')
            ->limit(3)
            ->get();

        $peoresDias = ProduccionHuevos::select('Fecha', DB::raw('SUM(CantidadHuevos) as total'))
            ->whereBetween('Fecha', [$from, $to])
            ->when($this->isEmployeeContext($request), function($q) use ($request) {
                $q->whereIn('IDLote', $this->permittedLotIds($request));
            })
            ->when($loteId, fn($q) => $q->where('IDLote', $loteId))
            ->when($turno, fn($q) => $q->where('Turno', $turno))
            ->groupBy('Fecha')
            ->orderBy('total')
            ->limit(3)
            ->get();

        if ($this->isEmployeeContext($request)) {
            $allowedLotIds = $this->permittedLotIds($request);
            $lotes = Lote::whereIn('IDLote', $allowedLotIds)->orderBy('Nombre')->get(['IDLote','Nombre']);
        } else {
            $lotes = Lote::orderBy('Nombre')->get(['IDLote','Nombre']);
        }

        return view('admin.produccion_huevos.index', [
            'producciones' => $producciones,
            'lotes' => $lotes,
            'filters' => [
                'from' => $from,
                'to' => $to,
                'lote' => $loteId,
                'turno' => $turno,
            ],
            'totales' => $totales,
            'porcentajeRotos' => $porcentajeRotos,
            'serieDiaria' => $serieDiaria,
            'mejoresDias' => $mejoresDias,
            'peoresDias' => $peoresDias,
        ]);
    }

    public function create(Request $request)
    {
        if ($this->isEmployeeContext($request)) {
            $allowedLotIds = $this->permittedLotIds($request);
            $lotes = Lote::whereIn('IDLote', $allowedLotIds)->orderBy('Nombre')->get(['IDLote','Nombre']);
        } else {
            $lotes = Lote::orderBy('Nombre')->get(['IDLote','Nombre']);
        }
        $hoy = Carbon::now()->toDateString();

        return view('admin.produccion_huevos.create', [
            'lotes' => $lotes,
            'hoy' => $hoy,
        ]);
    }

    public function store(StoreProduccionHuevosRequest $request)
    {
        try {
            $data = $request->validated();
            $data['IDUsuario'] = Auth::id();
            // Por defecto, si no viene Fecha del request, usar hoy
            $data['Fecha'] = $data['Fecha'] ?? Carbon::now()->toDateString();

            if ($this->isEmployeeContext($request)) {
                $allowedLotIds = $this->permittedLotIds($request);
                if (!$allowedLotIds->contains($data['IDLote'])) {
                    return back()->withInput()->with('error', 'No tienes permiso para registrar producción en este lote.');
                }
            }

            ProduccionHuevos::create($data);

            $redirect = $this->isEmployeeContext($request) ? 'employee.produccion-huevos.index' : 'admin.produccion-huevos.index';
            return redirect()
                ->route($redirect)
                ->with('success', 'Producción de huevos registrada correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error registrando producción de huevos', [
                'message' => $e->getMessage(),
            ]);
            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error al guardar el registro.');
        }
    }

    public function exportCsv(Request $request)
    {
        // Mismos filtros que index
        $from = $request->input('from', Carbon::now()->subDays(7)->toDateString());
        $to = $request->input('to', Carbon::now()->toDateString());
        $loteId = $request->input('lote');
        $turno = $request->input('turno');

        $rows = ProduccionHuevos::with('lote')
            ->whereBetween('Fecha', [$from, $to])
            ->when($loteId, fn($q) => $q->where('IDLote', $loteId))
            ->when($turno, fn($q) => $q->where('Turno', $turno))
            ->orderBy('Fecha')
            ->get([ 'Fecha','IDLote','CantidadHuevos','HuevosRotos','Turno','PesoPromedio','PorcentajePostura','Observaciones' ]);

        $filename = 'produccion_huevos_'.Carbon::now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($rows) {
            $output = fopen('php://output', 'w');
            // BOM UTF-8 para Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, [
                'Fecha','Lote','CantidadHuevos','HuevosRotos','Turno','PesoPromedio','PorcentajePostura','Observaciones'
            ]);
            foreach ($rows as $r) {
                fputcsv($output, [
                    Carbon::parse($r->Fecha)->format('Y-m-d'),
                    optional($r->lote)->Nombre ?? $r->IDLote,
                    $r->CantidadHuevos,
                    $r->HuevosRotos,
                    $r->Turno,
                    $r->PesoPromedio,
                    $r->PorcentajePostura,
                    $r->Observaciones,
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}
