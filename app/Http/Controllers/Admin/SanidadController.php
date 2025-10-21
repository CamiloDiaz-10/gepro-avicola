<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sanidad;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FiltroFincasHelper;

class SanidadController extends Controller
{
    use FiltroFincasHelper;

    public function index(Request $request)
    {
        $query = Sanidad::query()->with('lote');
        // Aplicar filtro por fincas asignadas (no admins ven solo lo suyo)
        $query = $this->aplicarFiltroFincas($query);

        if ($request->filled('tipo')) {
            $query->porTipo($request->input('tipo'));
        }
        if ($request->filled('lote')) {
            $query->porLote($request->input('lote'));
        }
        if ($request->filled('desde') || $request->filled('hasta')) {
            $desde = $request->input('desde', now()->subDays(30)->toDateString());
            $hasta = $request->input('hasta', now()->toDateString());
            $query->whereBetween('Fecha', [$desde, $hasta]);
        }

        $treatments = $query->orderByDesc('Fecha')->paginate(10)->withQueryString();
        // Lotes accesibles según asignación
        $lotes = $this->getLotesAccesibles()->map(function($l){ return (object)['IDLote'=>$l->IDLote,'Nombre'=>$l->Nombre]; });
        $tipos = ['Vacuna','Desparasitante','Vitamina','Otro'];
        
        return view('admin.sanidad.index', compact('treatments', 'lotes', 'tipos'));
    }

    public function create()
    {
        // Solo lotes accesibles
        $lotes = $this->getLotesAccesibles()->map(function($l){ return (object)['IDLote'=>$l->IDLote,'Nombre'=>$l->Nombre]; });
        $tipos = ['Vacuna','Desparasitante','Vitamina','Otro'];
        return view('admin.sanidad.create', compact('lotes', 'tipos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'IDLote' => ['required', 'exists:lotes,IDLote'],
            'Fecha' => ['required', 'date'],
            'Producto' => ['required', 'string', 'max:100'],
            'TipoTratamiento' => ['nullable', 'string', 'max:50'],
            'Dosis' => ['nullable', 'string', 'max:50'],
            'Observaciones' => ['nullable', 'string'],
        ], [
            'IDLote.required' => 'El lote es obligatorio.',
            'IDLote.exists' => 'El lote seleccionado no existe.',
            'Fecha.required' => 'La fecha es obligatoria.',
            'Producto.required' => 'El producto es obligatorio.',
        ]);

        // Verificar acceso al lote seleccionado según fincas asignadas
        if (!$this->verificarAccesoLote($data['IDLote'])) {
            abort(403, 'No tienes permiso para registrar tratamientos en este lote.');
        }

        $data['IDUsuario'] = auth()->id();

        Sanidad::create($data);

        return redirect()->route('admin.sanidad.index')
            ->with('success', 'Tratamiento registrado correctamente.');
    }

    public function edit(Sanidad $sanidad)
    {
        // Verificar acceso al lote del registro
        if (!$this->verificarAccesoLote($sanidad->IDLote)) {
            abort(403, 'No tienes permiso para editar este tratamiento.');
        }
        // Solo lotes accesibles
        $lotes = $this->getLotesAccesibles()->map(function($l){ return (object)['IDLote'=>$l->IDLote,'Nombre'=>$l->Nombre]; });
        $tipos = ['Vacuna','Desparasitante','Vitamina','Otro'];
        return view('admin.sanidad.edit', [
            'treatment' => $sanidad,
            'lotes' => $lotes,
            'tipos' => $tipos,
        ]);
    }

    public function update(Request $request, Sanidad $sanidad)
    {
        if (!$this->verificarAccesoLote($sanidad->IDLote)) {
            abort(403, 'No tienes permiso para actualizar este tratamiento.');
        }
        $data = $request->validate([
            'IDLote' => ['required', 'exists:lotes,IDLote'],
            'Fecha' => ['required', 'date'],
            'Producto' => ['required', 'string', 'max:100'],
            'TipoTratamiento' => ['nullable', 'string', 'max:50'],
            'Dosis' => ['nullable', 'string', 'max:50'],
            'Observaciones' => ['nullable', 'string'],
        ]);

        // Si cambia de lote, verificar acceso al nuevo lote
        if (!$this->verificarAccesoLote($data['IDLote'])) {
            abort(403, 'No tienes permiso para asignar este tratamiento al lote seleccionado.');
        }

        $sanidad->update($data);

        return redirect()->route('admin.sanidad.index')
            ->with('success', 'Tratamiento actualizado correctamente.');
    }

    public function destroy(Sanidad $sanidad)
    {
        if (!$this->verificarAccesoLote($sanidad->IDLote)) {
            abort(403, 'No tienes permiso para eliminar este tratamiento.');
        }
        $sanidad->delete();
        return redirect()->route('admin.sanidad.index')
            ->with('success', 'Tratamiento eliminado correctamente.');
    }
}
