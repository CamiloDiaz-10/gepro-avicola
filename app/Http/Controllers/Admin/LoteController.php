<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\Finca;
use App\Traits\FiltroFincasHelper;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    use FiltroFincasHelper;

    // Listado de lotes
    public function index(Request $request)
    {
        $query = Lote::with('finca');

        // Aplicar filtro de fincas según usuario
        $query = $this->aplicarFiltroFincas($query);

        if ($request->filled('finca')) {
            $query->where('IDFinca', $request->integer('finca'));
        }
        if ($request->filled('search')) {
            $s = $request->string('search');
            $query->where(function ($q) use ($s) {
                $q->where('Nombre', 'like', "%{$s}%")
                  ->orWhere('Raza', 'like', "%{$s}%");
            });
        }

        $lotes = $query->orderByDesc('created_at')->paginate(12);
        
        // Obtener solo fincas accesibles para el usuario
        $fincas = $this->getFincasAccesibles();

        return view('admin.lotes.index', compact('lotes', 'fincas'));
    }

    // Form crear
    public function create()
    {
        // Solo mostrar fincas a las que el usuario tiene acceso
        $fincas = $this->getFincasAccesibles();
        
        if ($fincas->isEmpty() && !auth()->user()->hasRole('Administrador')) {
            return redirect()->route('sin-fincas')
                ->with('error', 'No tienes fincas asignadas para crear lotes.');
        }
        
        return view('admin.lotes.create', compact('fincas'));
    }

    // Guardar
    public function store(Request $request)
    {
        $validated = $request->validate([
            'IDFinca' => 'required|exists:fincas,IDFinca',
            'Nombre' => 'required|string|max:100',
            'FechaIngreso' => 'required|date',
            'CantidadInicial' => 'required|integer|min:1',
            'Raza' => 'nullable|string|max:50',
        ], [
            'IDFinca.required' => 'La finca es obligatoria.',
            'IDFinca.exists' => 'La finca seleccionada no existe.',
            'Nombre.required' => 'El nombre del lote es obligatorio.',
            'FechaIngreso.required' => 'La fecha de ingreso es obligatoria.',
            'CantidadInicial.required' => 'La cantidad inicial es obligatoria.',
        ]);

        // Para propietarios y empleados, verificar que la finca esté en su lista accesible
        $user = auth()->user();
        if ($user && !$user->hasRole('Administrador')) {
            $fincasAccesibles = $user->fincas()->pluck('fincas.IDFinca')->toArray();
            
            if (!in_array($validated['IDFinca'], $fincasAccesibles)) {
                \Log::warning('Usuario intentó crear lote en finca no asignada', [
                    'user_id' => $user->IDUsuario,
                    'finca_id' => $validated['IDFinca'],
                    'fincas_accesibles' => $fincasAccesibles
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'No tienes permiso para crear lotes en esta finca.');
            }
        }

        try {
            $lote = Lote::create($validated);

            // Redirigir según el contexto del usuario
            $route = 'admin.lotes.index';
            if ($request->routeIs('owner.*')) {
                $route = 'owner.lotes.index';
            } elseif ($request->routeIs('employee.*')) {
                $route = 'employee.lotes.index';
            }

            \Log::info('Lote creado exitosamente', [
                'lote_id' => $lote->IDLote,
                'user_id' => $user->IDUsuario ?? null,
                'finca_id' => $validated['IDFinca']
            ]);

            return redirect()->route($route)
                ->with('success', 'Lote creado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al crear lote: ' . $e->getMessage(), [
                'validated' => $validated,
                'user_id' => $user->IDUsuario ?? null
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el lote: ' . $e->getMessage());
        }
    }

    // Ver
    public function show(Lote $lote)
    {
        // Verificar acceso al lote
        if (!$this->verificarAccesoLote($lote->IDLote)) {
            abort(403, 'No tienes permiso para ver este lote.');
        }

        // Cargar relaciones necesarias
        $lote->load(['finca', 'gallinas.tipoGallina']);
        
        // Obtener conteos directos desde la base de datos
        $conteos = \DB::table('gallinas')
            ->select(
                \DB::raw('COUNT(*) as total'),
                \DB::raw('SUM(CASE WHEN LOWER(TRIM(Estado)) = "activa" OR LOWER(TRIM(Estado)) = "activo" THEN 1 ELSE 0 END) as activas'),
                \DB::raw('SUM(CASE WHEN LOWER(TRIM(Estado)) = "muerta" OR LOWER(TRIM(Estado)) = "muerto" THEN 1 ELSE 0 END) as inactivas'),
                \DB::raw('SUM(CASE WHEN LOWER(TRIM(Estado)) LIKE "%vendida%" OR LOWER(TRIM(Estado)) LIKE "%vendido%" THEN 1 ELSE 0 END) as vendidas')
            )
            ->where('IDLote', $lote->IDLote)
            ->first();
        
        // Si no hay resultados, inicializar con ceros
        $totalGallinas = $conteos->total ?? 0;
        $gallinasActivas = $conteos->activas ?? 0;
        $gallinasInactivas = $conteos->inactivas ?? 0;
        $gallinasVendidas = $conteos->vendidas ?? 0;
        
        // Calcular aves realmente activas (total - inactivas - vendidas)
        $gallinasActivas = $totalGallinas - $gallinasInactivas - $gallinasVendidas;
        
        // Estadísticas por tipo de gallina
        $porTipo = $lote->gallinas->groupBy('IDTipoGallina')->map(function($grupo) {
            return [
                'tipo' => $grupo->first()->tipoGallina->Nombre ?? 'Sin tipo',
                'cantidad' => $grupo->count(),
                'porcentaje' => round(($grupo->count() / $grupo->sum(function() { return 1; })) * 100, 1)
            ];
        })->sortByDesc('cantidad');
        
        // Registrar para depuración
        \Log::info('Estadísticas del lote ' . $lote->IDLote . ' (SQL):', [
            'total' => $totalGallinas,
            'activas' => $gallinasActivas,
            'inactivas' => $gallinasInactivas,
            'vendidas' => $gallinasVendidas,
            'query' => 'SELECT IDLote, COUNT(*) as total, ' .
                      'SUM(CASE WHEN LOWER(TRIM(Estado)) = "activa" OR LOWER(TRIM(Estado)) = "activo" THEN 1 ELSE 0 END) as activas, ' .
                      'SUM(CASE WHEN LOWER(TRIM(Estado)) = "muerta" OR LOWER(TRIM(Estado)) = "muerto" THEN 1 ELSE 0 END) as inactivas, ' .
                      'SUM(CASE WHEN LOWER(TRIM(Estado)) LIKE "%vendida%" OR LOWER(TRIM(Estado)) LIKE "%vendido%" THEN 1 ELSE 0 END) as vendidas ' .
                      'FROM gallinas WHERE IDLote = ' . $lote->IDLote
        ]);
        
        // Pasar las variables a la vista
        return view('admin.lotes.show', compact(
            'lote', 
            'totalGallinas', 
            'gallinasActivas', 
            'gallinasInactivas',
            'gallinasVendidas',
            'porTipo'
        ));
        $distribucionEdad = $lote->gallinas->groupBy(function($gallina) {
            $semanas = $gallina->edad_en_semanas;
            if ($semanas < 4) return '0-3 semanas';
            if ($semanas < 12) return '4-11 semanas';
            if ($semanas < 24) return '3-5 meses';
            if ($semanas < 52) return '6-12 meses';
            return 'Más de 1 año';
        })->map(function($grupo, $rango) {
            return [
                'rango' => $rango,
                'cantidad' => $grupo->count(),
                'porcentaje' => round(($grupo->count() / $grupo->sum(function() { return 1; })) * 100, 1)
            ];
        })->sortBy('rango');
        
        // Peso promedio
        $pesoPromedio = $lote->gallinas->avg('Peso');
        
        // Tendencias de producción (últimos 7 días)
        $fechas = collect(range(6, 0))->map(function($dias) {
            return now()->subDays($dias)->format('Y-m-d');
        });
        
        $tendencias = [];
        foreach ($fechas as $fecha) {
            $tendencias[$fecha] = [
                'fecha' => $fecha,
                'total' => 0,
                'activas' => 0,
                'inactivas' => 0
            ];
        }
        
        // Agrupar por fecha de creación
        $lote->gallinas->each(function($gallina) use (&$tendencias) {
            $fecha = $gallina->created_at->format('Y-m-d');
            if (isset($tendencias[$fecha])) {
                $tendencias[$fecha]['total']++;
                if ($gallina->Estado === 'Activa') {
                    $tendencias[$fecha]['activas']++;
                } else {
                    $tendencias[$fecha]['inactivas']++;
                }
            }
        });
        
        // Mortalidad vs. cantidad inicial
        $mortalidad = [
            'inicial' => $lote->CantidadInicial,
            'actual' => $totalGallinas,
            'diferencia' => $lote->CantidadInicial - $totalGallinas,
            'porcentaje_mortalidad' => $lote->CantidadInicial > 0 ? 
                round((($lote->CantidadInicial - $totalGallinas) / $lote->CantidadInicial) * 100, 2) : 0
        ];
        
        return view('admin.lotes.show', compact(
            'lote', 
            'totalGallinas', 
            'gallinasActivas', 
            'gallinasInactivas',
            'porTipo',
            'distribucionEdad',
            'pesoPromedio',
            'tendencias',
            'mortalidad'
        ));
    }

    // Form editar
    public function edit(Lote $lote)
    {
        // Verificar acceso al lote
        if (!$this->verificarAccesoLote($lote->IDLote)) {
            abort(403, 'No tienes permiso para editar este lote.');
        }

        $fincas = $this->getFincasAccesibles();
        return view('admin.lotes.edit', compact('lote', 'fincas'));
    }

    // Actualizar
    public function update(Request $request, Lote $lote)
    {
        // Verificar acceso al lote original
        if (!$this->verificarAccesoLote($lote->IDLote)) {
            return redirect()->back()
                ->with('error', 'No tienes permiso para editar este lote.');
        }

        $validated = $request->validate([
            'IDFinca' => 'required|exists:fincas,IDFinca',
            'Nombre' => 'required|string|max:100',
            'FechaIngreso' => 'required|date',
            'CantidadInicial' => 'required|integer|min:1',
            'Raza' => 'nullable|string|max:50',
        ]);

        // Verificar acceso a la nueva finca si cambió
        if ($lote->IDFinca != $validated['IDFinca']) {
            if (!$this->verificarAccesoFinca($validated['IDFinca'])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'No tienes permiso para mover el lote a esa finca.');
            }
        }

        try {
            $lote->update($validated);

            // Redirigir según el contexto del usuario
            $route = 'admin.lotes.index';
            if ($request->routeIs('owner.*')) {
                $route = 'owner.lotes.index';
            } elseif ($request->routeIs('employee.*')) {
                $route = 'employee.lotes.index';
            }

            return redirect()->route($route)
                ->with('success', 'Lote actualizado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar lote: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el lote. Por favor, intenta de nuevo.');
        }
    }

    // Eliminar
    public function destroy(Request $request, Lote $lote)
    {
        // Verificar acceso al lote
        if (!$this->verificarAccesoLote($lote->IDLote)) {
            return redirect()->back()
                ->with('error', 'No tienes permiso para eliminar este lote.');
        }

        try {
            $lote->delete();

            // Redirigir según el contexto del usuario
            $route = 'admin.lotes.index';
            if ($request->routeIs('owner.*')) {
                $route = 'owner.lotes.index';
            } elseif ($request->routeIs('employee.*')) {
                $route = 'employee.lotes.index';
            }

            return redirect()->route($route)
                ->with('success', 'Lote eliminado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al eliminar lote: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al eliminar el lote. Puede que tenga aves asociadas.');
        }
    }
}
