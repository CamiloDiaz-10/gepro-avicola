<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sanidad;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SanidadController extends Controller
{
    public function index(Request $request)
    {
        $query = Sanidad::query()->with('lote');

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
        $lotes = Lote::query()->orderBy('Nombre')->get(['IDLote', 'Nombre']);

        return view('admin.sanidad.index', compact('treatments', 'lotes'));
    }

    public function create()
    {
        $lotes = Lote::query()->orderBy('Nombre')->get(['IDLote', 'Nombre']);
        return view('admin.sanidad.create', compact('lotes'));
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

        $data['IDUsuario'] = auth()->id();

        Sanidad::create($data);

        return redirect()->route('admin.sanidad.index')
            ->with('success', 'Tratamiento registrado correctamente.');
    }

    public function edit(Sanidad $sanidad)
    {
        $lotes = Lote::query()->orderBy('Nombre')->get(['IDLote', 'Nombre']);
        return view('admin.sanidad.edit', [
            'treatment' => $sanidad,
            'lotes' => $lotes,
        ]);
    }

    public function update(Request $request, Sanidad $sanidad)
    {
        $data = $request->validate([
            'IDLote' => ['required', 'exists:lotes,IDLote'],
            'Fecha' => ['required', 'date'],
            'Producto' => ['required', 'string', 'max:100'],
            'TipoTratamiento' => ['nullable', 'string', 'max:50'],
            'Dosis' => ['nullable', 'string', 'max:50'],
            'Observaciones' => ['nullable', 'string'],
        ]);

        $sanidad->update($data);

        return redirect()->route('admin.sanidad.index')
            ->with('success', 'Tratamiento actualizado correctamente.');
    }

    public function destroy(Sanidad $sanidad)
    {
        $sanidad->delete();
        return redirect()->route('admin.sanidad.index')
            ->with('success', 'Tratamiento eliminado correctamente.');
    }
}
