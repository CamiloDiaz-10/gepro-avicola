<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\Finca;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    // Listado de lotes
    public function index(Request $request)
    {
        $query = Lote::with('finca');

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
        $fincas = Finca::orderBy('Nombre')->get();

        return view('admin.lotes.index', compact('lotes', 'fincas'));
    }

    // Form crear
    public function create()
    {
        $fincas = Finca::orderBy('Nombre')->get();
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

        $lote = Lote::create($validated);

        return redirect()->route('admin.lotes.index')
            ->with('success', 'Lote creado correctamente.');
    }

    // Ver
    public function show(Lote $lote)
    {
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
        $fincas = Finca::orderBy('Nombre')->get();
        return view('admin.lotes.edit', compact('lote', 'fincas'));
    }

    // Actualizar
    public function update(Request $request, Lote $lote)
    {
        $validated = $request->validate([
            'IDFinca' => 'required|exists:fincas,IDFinca',
            'Nombre' => 'required|string|max:100',
            'FechaIngreso' => 'required|date',
            'CantidadInicial' => 'required|integer|min:1',
            'Raza' => 'nullable|string|max:50',
        ]);

        $lote->update($validated);

        return redirect()->route('admin.lotes.index')
            ->with('success', 'Lote actualizado correctamente.');
    }

    // Eliminar
    public function destroy(Lote $lote)
    {
        $lote->delete();
        return redirect()->route('admin.lotes.index')
            ->with('success', 'Lote eliminado correctamente.');
    }
}
