<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index', [
            'user' => Auth::user()
        ]);
    }

    public function settings()
    {
        $roles = Role::all();
        return view('profile.settings', [
            'user' => Auth::user(),
            'roles' => $roles
        ]);
    }

    public function update(Request $request)
    {
        $messages = [
            'TipoIdentificacion.required' => 'El tipo de identificación es obligatorio.',
            'NumeroIdentificacion.required' => 'El número de identificación es obligatorio.',
            'NumeroIdentificacion.unique' => 'Este número de identificación ya está registrado.',
            'Nombre.required' => 'El nombre es obligatorio.',
            'Apellido.required' => 'El apellido es obligatorio.',
            'Email.required' => 'El correo electrónico es obligatorio.',
            'Email.unique' => 'Este correo electrónico ya está registrado.',
            'Telefono.regex' => 'El teléfono debe contener solo números (entre 8 y 15 dígitos) y opcionalmente el símbolo + al inicio.',
            'Telefono.required' => 'El teléfono es obligatorio.',
            'FechaNacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
        ];

        $validated = $request->validate([
            'TipoIdentificacion' => 'required|string|max:50',
            'NumeroIdentificacion' => 'required|string|max:50|unique:usuarios,NumeroIdentificacion,' . Auth::id() . ',IDUsuario',
            'Nombre' => 'required|string|max:50',
            'Apellido' => 'required|string|max:50',
            'Email' => 'required|string|email|max:150|unique:usuarios,Email,' . Auth::id() . ',IDUsuario',
            'Telefono' => ['required', 'string', 'regex:/^[+]?([0-9]{8,15})$/', 'max:20'],
            'FechaNacimiento' => 'nullable|date|before:today',
            'Direccion' => 'nullable|string|max:500',
        ], $messages);

        // Limpiar el número de teléfono antes de guardarlo
        $telefono = preg_replace('/[^0-9+]/', '', $request->Telefono);

        Auth::user()->update([
            'TipoIdentificacion' => $request->TipoIdentificacion,
            'NumeroIdentificacion' => $request->NumeroIdentificacion,
            'Nombre' => $request->Nombre,
            'Apellido' => $request->Apellido,
            'Email' => $request->Email,
            'Telefono' => $telefono,
            'FechaNacimiento' => $request->FechaNacimiento,
            'Direccion' => $request->Direccion,
        ]);

        return back()->with('status', 'Perfil actualizado exitosamente.');
    }

    public function updatePassword(Request $request)
    {
        $messages = [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.'
        ];

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], $messages);

        // Verificar la contraseña actual manualmente
        if (!Hash::check($request->current_password, Auth::user()->Contrasena)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        Auth::user()->update([
            'Contrasena' => Hash::make($request->password)
        ]);

        return back()->with('status', 'Contraseña actualizada exitosamente.');
    }
}