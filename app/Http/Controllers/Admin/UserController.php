<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Finca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // El middleware se aplica en las rutas

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'fincas']);

        // Filtros
        if ($request->filled('role')) {
            $query->where('IDRol', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('Nombre', 'LIKE', "%{$search}%")
                  ->orWhere('Apellido', 'LIKE', "%{$search}%")
                  ->orWhere('Email', 'LIKE', "%{$search}%")
                  ->orWhere('NumeroIdentificacion', 'LIKE', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        $fincas = Finca::all();
        
        return view('admin.users.create', compact('roles', 'fincas'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'TipoIdentificacion' => 'required|in:CC,CE,TI,PP',
            'NumeroIdentificacion' => 'required|string|max:50|unique:usuarios,NumeroIdentificacion',
            'Nombre' => 'required|string|max:100',
            'Apellido' => 'required|string|max:100',
            'Email' => 'required|email|max:150|unique:usuarios,Email',
            'Telefono' => 'nullable|string|max:20',
            'FechaNacimiento' => 'nullable|date|before:today',
            'Direccion' => 'nullable|string|max:500',
            'IDRol' => 'required|exists:roles,IDRol',
            'Contrasena' => 'required|string|min:6|confirmed',
            'fincas' => 'nullable|array',
            'fincas.*' => 'exists:fincas,IDFinca'
        ], [
            'TipoIdentificacion.required' => 'El tipo de identificación es obligatorio.',
            'NumeroIdentificacion.required' => 'El número de identificación es obligatorio.',
            'NumeroIdentificacion.unique' => 'Este número de identificación ya está registrado.',
            'Nombre.required' => 'El nombre es obligatorio.',
            'Apellido.required' => 'El apellido es obligatorio.',
            'Email.required' => 'El email es obligatorio.',
            'Email.unique' => 'Este email ya está registrado.',
            'IDRol.required' => 'El rol es obligatorio.',
            'Contrasena.required' => 'La contraseña es obligatoria.',
            'Contrasena.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'Contrasena.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        DB::beginTransaction();
        
        try {
            $user = User::create([
                'TipoIdentificacion' => $request->TipoIdentificacion,
                'NumeroIdentificacion' => $request->NumeroIdentificacion,
                'Nombre' => $request->Nombre,
                'Apellido' => $request->Apellido,
                'Email' => $request->Email,
                'Telefono' => $request->Telefono ? preg_replace('/[^0-9]/', '', $request->Telefono) : null,
                'FechaNacimiento' => $request->FechaNacimiento,
                'Direccion' => $request->Direccion,
                'IDRol' => $request->IDRol,
                'Contrasena' => Hash::make($request->Contrasena),
            ]);

            // Asignar fincas si se seleccionaron
            if ($request->filled('fincas')) {
                foreach ($request->fincas as $fincaId) {
                    DB::table('usuario_finca')->insert([
                        'IDUsuario' => $user->IDUsuario,
                        'IDFinca' => $fincaId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                           ->with('success', 'Usuario creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['role', 'fincas']);
        
        // Estadísticas del usuario basadas en sus fincas asignadas
        $fincaIds = $user->fincas->pluck('IDFinca')->toArray();
        
        $stats = [
            'fincas_asignadas' => count($fincaIds),
            'lotes_en_fincas' => DB::table('lotes')
                ->whereIn('IDFinca', $fincaIds)
                ->count(),
            'registros_produccion' => DB::table('produccion_huevos')
                ->join('lotes', 'produccion_huevos.IDLote', '=', 'lotes.IDLote')
                ->whereIn('lotes.IDFinca', $fincaIds)
                ->count(),
            'registros_sanidad' => DB::table('sanidad')
                ->join('lotes', 'sanidad.IDLote', '=', 'lotes.IDLote')
                ->whereIn('lotes.IDFinca', $fincaIds)
                ->count(),
            'ultimo_acceso' => $user->updated_at
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $fincas = Finca::all();
        $userFincas = $user->fincas->pluck('IDFinca')->toArray();
        
        return view('admin.users.edit', compact('user', 'roles', 'fincas', 'userFincas'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'TipoIdentificacion' => 'required|in:CC,CE,TI,PP',
            'NumeroIdentificacion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('usuarios', 'NumeroIdentificacion')->ignore($user->IDUsuario, 'IDUsuario')
            ],
            'Nombre' => 'required|string|max:100',
            'Apellido' => 'required|string|max:100',
            'Email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('usuarios', 'Email')->ignore($user->IDUsuario, 'IDUsuario')
            ],
            'Telefono' => 'nullable|string|max:20',
            'FechaNacimiento' => 'nullable|date|before:today',
            'Direccion' => 'nullable|string|max:500',
            'IDRol' => 'required|exists:roles,IDRol',
            'Contrasena' => 'nullable|string|min:6|confirmed',
            'fincas' => 'nullable|array',
            'fincas.*' => 'exists:fincas,IDFinca'
        ], [
            'NumeroIdentificacion.unique' => 'Este número de identificación ya está registrado.',
            'Email.unique' => 'Este email ya está registrado.',
            'Contrasena.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'Contrasena.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        DB::beginTransaction();
        
        try {
            $updateData = [
                'TipoIdentificacion' => $request->TipoIdentificacion,
                'NumeroIdentificacion' => $request->NumeroIdentificacion,
                'Nombre' => $request->Nombre,
                'Apellido' => $request->Apellido,
                'Email' => $request->Email,
                'Telefono' => $request->Telefono ? preg_replace('/[^0-9]/', '', $request->Telefono) : null,
                'FechaNacimiento' => $request->FechaNacimiento,
                'Direccion' => $request->Direccion,
                'IDRol' => $request->IDRol,
            ];

            // Solo actualizar contraseña si se proporcionó una nueva
            if ($request->filled('Contrasena')) {
                $updateData['Contrasena'] = Hash::make($request->Contrasena);
            }

            $user->update($updateData);

            // Actualizar asignación de fincas
            DB::table('usuario_finca')->where('IDUsuario', $user->IDUsuario)->delete();
            
            if ($request->filled('fincas')) {
                foreach ($request->fincas as $fincaId) {
                    DB::table('usuario_finca')->insert([
                        'IDUsuario' => $user->IDUsuario,
                        'IDFinca' => $fincaId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.users.index')
                           ->with('success', 'Usuario actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Verificar que no sea el usuario actual
        if ($user->IDUsuario === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        DB::beginTransaction();
        
        try {
            // Eliminar relaciones con fincas
            DB::table('usuario_finca')->where('IDUsuario', $user->IDUsuario)->delete();
            
            // Eliminar el usuario
            $user->delete();

            DB::commit();

            return redirect()->route('admin.users.index')
                           ->with('success', 'Usuario eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status (activate/deactivate)
     */
    public function toggleStatus(User $user)
    {
        try {
            $newStatus = $user->Estado === 'Activo' ? 'Inactivo' : 'Activo';
            $user->update(['Estado' => $newStatus]);

            $message = $newStatus === 'Activo' ? 'Usuario activado exitosamente.' : 'Usuario desactivado exitosamente.';
            
            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado del usuario.');
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user)
    {
        try {
            $newPassword = 'password123'; // Contraseña temporal
            $user->update([
                'Contrasena' => Hash::make($newPassword)
            ]);

            return back()->with('success', "Contraseña restablecida. Nueva contraseña: {$newPassword}");

        } catch (\Exception $e) {
            return back()->with('error', 'Error al restablecer la contraseña.');
        }
    }
}
