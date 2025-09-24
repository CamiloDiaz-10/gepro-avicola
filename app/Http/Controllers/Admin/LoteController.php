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
        $lote->load('finca');
        return view('admin.lotes.show', compact('lote'));
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
