<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finca;
use App\Models\User;
use Illuminate\Http\Request;

class FincaController extends Controller
{
    private function isEmployeeContext(Request $request): bool
    {
        $user = $request->user();
        return ($request->routeIs('employee.*')) || ($user && $user->role && $user->role->NombreRol === 'Empleado');
    }

    // Display a listing of the resource.
    public function index(Request $request)
    {
        $query = Finca::query();

        if ($this->isEmployeeContext($request)) {
            $assignedIds = $request->user()->fincas()->pluck('fincas.IDFinca');
            $query->whereIn('IDFinca', $assignedIds);
        }

        if ($request->filled('search')) {
            $s = $request->string('search');
            $query->where(function ($q) use ($s) {
                $q->where('Nombre', 'like', "%{$s}%")
                  ->orWhere('Ubicacion', 'like', "%{$s}%");
            });
        }

        $fincas = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('admin.fincas.index', compact('fincas'));
    }

    // Show the form for creating a new resource.
    public function create()
    {
        $users = User::orderBy('Nombre')->orderBy('Apellido')->get();
        return view('admin.fincas.create', compact('users'));
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Nombre' => 'required|string|max:100',
            'Ubicacion' => 'required|string|max:255',
            'Latitud' => 'nullable|numeric|between:-90,90',
            'Longitud' => 'nullable|numeric|between:-180,180',
            'Hectareas' => 'nullable|numeric|min:0',
        ], [
            'Nombre.required' => 'El nombre de la finca es obligatorio.',
            'Ubicacion.required' => 'La ubicación es obligatoria.',
        ]);

        $finca = Finca::create($validated);

        // Sync assigned users if provided
        $userIds = collect($request->input('users', []))
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();
        if (!empty($userIds)) {
            $finca->users()->sync($userIds);
        }

        return redirect()->route('admin.fincas.index')
            ->with('success', 'Finca creada correctamente.');
    }

    // Display the specified resource.
    public function show(Finca $finca)
    {
        $request = request();
        if ($this->isEmployeeContext($request)) {
            $assignedIds = $request->user()->fincas()->pluck('fincas.IDFinca');
            abort_unless($assignedIds->contains($finca->IDFinca), 403);
        }
        $finca->load('users');
        return view('admin.fincas.show', compact('finca'));
    }

    // Show the form for editing the specified resource.
    public function edit(Finca $finca)
    {
        $users = User::orderBy('Nombre')->orderBy('Apellido')->get();
        $selectedUsers = $finca->users()->pluck('usuarios.IDUsuario')->toArray();
        return view('admin.fincas.edit', compact('finca', 'users', 'selectedUsers'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, Finca $finca)
    {
        $validated = $request->validate([
            'Nombre' => 'required|string|max:100',
            'Ubicacion' => 'required|string|max:255',
            'Latitud' => 'nullable|numeric|between:-90,90',
            'Longitud' => 'nullable|numeric|between:-180,180',
            'Hectareas' => 'nullable|numeric|min:0',
        ]);

        $finca->update($validated);

        // Sync assigned users
        $userIds = collect($request->input('users', []))
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();
        $finca->users()->sync($userIds);

        return redirect()->route('admin.fincas.index')
            ->with('success', 'Finca actualizada correctamente.');
    }

    // Remove the specified resource from storage.
    public function destroy(Finca $finca)
    {
        try {
            $finca->delete();

            return redirect()->route('admin.fincas.index')
                ->with('success', 'Finca eliminada correctamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Código SQLSTATE 23000: violación de integridad (FK)
            if ($e->getCode() === '23000') {
                $mensaje = 'No se puede eliminar la finca porque tiene registros relacionados.';
                // Intentar detectar relaciones comunes para mensaje más claro
                try {
                    $relaciones = [];
                    $usuariosAsignados = $finca->users()->count();
                    if ($usuariosAsignados > 0) { $relaciones[] = 'usuarios asignados'; }
                    if (\Schema::hasTable('lotes')) {
                        $lotes = \DB::table('lotes')->where('IDFinca', $finca->IDFinca)->count();
                        if ($lotes > 0) { $relaciones[] = 'lotes'; }
                    }
                    if (!empty($relaciones)) {
                        $mensaje .= ' Relacionados: ' . implode(', ', $relaciones) . '. Desasigne o elimine esos registros primero.';
                    }
                } catch (\Throwable $t) {
                    // Ignorar detección detallada si falla
                }
                return redirect()->route('admin.fincas.index')->with('error', $mensaje);
            }
            // Otros errores
            return redirect()->route('admin.fincas.index')->with('error', 'No se pudo eliminar la finca.');
        }
    }
}
