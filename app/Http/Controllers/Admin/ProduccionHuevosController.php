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
    private function isOwnerContext(Request $request): bool
    {
        $user = $request->user();
        return ($request->routeIs('owner.*')) || ($user && $user->role && $user->role->NombreRol === 'Propietario');
    }
    private function isEmployeeContext(Request $request): bool
    {
        $user = $request->user();
        return ($request->routeIs('employee.*')) || ($user && $user->role && $user->role->NombreRol === 'Empleado');
    }

    /**
     * Verifica si el usuario necesita filtrado por fincas (no es admin)
     */
    private function needsFincaFilter(Request $request): bool
    {
        $user = $request->user();
        if (!$user) return false;
        
        // Admin no necesita filtro, propietarios y empleados SÍ
        return !$user->hasRole('Administrador');
    }

    private function permittedLotIds(Request $request)
    {
        $user = $request->user();
        if (!$user) return collect();

        // Lotes pertenecientes a las fincas asignadas al usuario (propietario o empleado)
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

        // Filtrar por fincas asignadas (propietarios y empleados)
        if ($this->needsFincaFilter($request)) {
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
         ->when($this->needsFincaFilter($request), function($q) use ($request) {
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
            ->when($this->needsFincaFilter($request), function($q) use ($request) {
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
            ->when($this->needsFincaFilter($request), function($q) use ($request) {
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
            ->when($this->needsFincaFilter($request), function($q) use ($request) {
                $q->whereIn('IDLote', $this->permittedLotIds($request));
            })
            ->when($loteId, fn($q) => $q->where('IDLote', $loteId))
            ->when($turno, fn($q) => $q->where('Turno', $turno))
            ->groupBy('Fecha')
            ->orderBy('total')
            ->limit(3)
            ->get();

        // Estadísticas por lote: Mayor y menor producción
        $produccionPorLote = ProduccionHuevos::select(
                'IDLote', 
                DB::raw('SUM(CantidadHuevos) as total_producido'),
                DB::raw('AVG(CantidadHuevos) as promedio_diario'),
                DB::raw('COUNT(*) as dias_registrados')
            )
            ->whereBetween('Fecha', [$from, $to])
            ->when($this->needsFincaFilter($request), function($q) use ($request) {
                $q->whereIn('IDLote', $this->permittedLotIds($request));
            })
            ->when($turno, fn($q) => $q->where('Turno', $turno))
            ->groupBy('IDLote')
            ->having('total_producido', '>', 0)
            ->get();

        // Obtener lote con mayor y menor producción
        // Solo si hay al menos 2 lotes diferentes para comparar
        $mejorLote = null;
        $peorLote = null;
        
        if ($produccionPorLote->count() >= 2) {
            // Ordenar por producción total
            $lotesOrdenados = $produccionPorLote->sortByDesc('total_producido')->values();
            
            // Lote con MAYOR producción (primero después de ordenar descendente)
            $mejorLoteData = $lotesOrdenados->first();
            $mejorLote = Lote::with('finca')->find($mejorLoteData->IDLote);
            if ($mejorLote) {
                $mejorLote->total_producido = $mejorLoteData->total_producido;
                $mejorLote->promedio_diario = round($mejorLoteData->promedio_diario, 2);
                $mejorLote->dias_registrados = $mejorLoteData->dias_registrados;
            }
            
            // Lote con MENOR producción (último después de ordenar descendente)
            $peorLoteData = $lotesOrdenados->last();
            $peorLote = Lote::with('finca')->find($peorLoteData->IDLote);
            if ($peorLote) {
                $peorLote->total_producido = $peorLoteData->total_producido;
                $peorLote->promedio_diario = round($peorLoteData->promedio_diario, 2);
                $peorLote->dias_registrados = $peorLoteData->dias_registrados;
            }
            
            // Verificar que no sean el mismo lote (por si acaso)
            if ($mejorLote && $peorLote && $mejorLote->IDLote === $peorLote->IDLote) {
                // Si es el mismo lote, no mostrar ninguno
                $mejorLote = null;
                $peorLote = null;
            }
        }

        // Lista de lotes para el dropdown (filtrados por fincas asignadas y excluyendo engorde)
        if ($this->needsFincaFilter($request)) {
            $allowedLotIds = $this->permittedLotIds($request);
            $lotes = Lote::with('gallinas.tipoGallina')
                ->whereIn('IDLote', $allowedLotIds)
                ->get()
                ->filter(function($lote) {
                    return $lote->puede_producir_huevos;
                })
                ->sortBy('Nombre')
                ->values();
        } else {
            $lotes = Lote::with('gallinas.tipoGallina')
                ->get()
                ->filter(function($lote) {
                    return $lote->puede_producir_huevos;
                })
                ->sortBy('Nombre')
                ->values();
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
            'mejorLote' => $mejorLote,
            'peorLote' => $peorLote,
            'produccionPorLote' => $produccionPorLote,
        ]);
    }

    public function create(Request $request)
    {
        // Lista de lotes filtrados por fincas asignadas y excluyendo lotes de engorde
        if ($this->needsFincaFilter($request)) {
            $allowedLotIds = $this->permittedLotIds($request);
            $lotes = Lote::with(['gallinas.tipoGallina', 'finca'])
                ->whereIn('IDLote', $allowedLotIds)
                ->orderBy('Nombre')
                ->get()
                ->filter(function($lote) {
                    return $lote->puede_producir_huevos;
                })
                ->values();
        } else {
            $lotes = Lote::with(['gallinas.tipoGallina', 'finca'])
                ->orderBy('Nombre')
                ->get()
                ->filter(function($lote) {
                    return $lote->puede_producir_huevos;
                })
                ->values();
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

            // Verificar acceso al lote (propietarios y empleados)
            if ($this->needsFincaFilter($request)) {
                $allowedLotIds = $this->permittedLotIds($request);
                if (!$allowedLotIds->contains($data['IDLote'])) {
                    return back()->withInput()->with('error', 'No tienes permiso para registrar producción en este lote.');
                }
            }

            ProduccionHuevos::create($data);

            // Redirigir según el contexto del usuario
            $redirect = 'admin.produccion-huevos.index';
            if ($this->isOwnerContext($request)) {
                $redirect = 'owner.produccion-huevos.index';
            } elseif ($this->isEmployeeContext($request)) {
                $redirect = 'employee.produccion-huevos.index';
            }
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
            ->when($this->needsFincaFilter($request), function($q) use ($request) {
                $q->whereIn('IDLote', $this->permittedLotIds($request));
            })
            ->when($loteId, fn($q) => $q->where('IDLote', $loteId))
            ->when($turno, fn($q) => $q->where('Turno', $turno))
            ->orderBy('Fecha')
            ->get([ 'Fecha','IDLote','CantidadHuevos','HuevosRotos','Turno','PesoPromedio','Observaciones' ]);

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
                'Fecha','Lote','CantidadHuevos','HuevosRotos','Turno','PesoPromedio','Observaciones'
            ]);
            foreach ($rows as $r) {
                fputcsv($output, [
                    Carbon::parse($r->Fecha)->format('Y-m-d'),
                    optional($r->lote)->Nombre ?? $r->IDLote,
                    $r->CantidadHuevos,
                    $r->HuevosRotos,
                    $r->Turno,
                    $r->PesoPromedio,
                    $r->Observaciones,
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(Request $request, $id)
    {
        try {
            // Bloquear contexto de empleado explícitamente
            if ($this->isEmployeeContext($request)) {
                abort(403);
            }

            $record = ProduccionHuevos::findOrFail($id);

            // Si es propietario, validar que el registro pertenezca a un lote permitido
            if ($this->isOwnerContext($request)) {
                $allowedLotIds = $this->permittedLotIds($request);
                abort_unless($allowedLotIds->contains((int) $record->IDLote), 403);
            }

            $record->delete();

            $redirect = $this->isOwnerContext($request) ? 'owner.produccion-huevos.index' : 'admin.produccion-huevos.index';
            return redirect()->route($redirect)->with('success', 'Registro de producción eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error eliminando producción de huevos', ['id' => $id, 'err' => $e->getMessage()]);
            return back()->with('error', 'No se pudo eliminar el registro.');
        }
    }
}
