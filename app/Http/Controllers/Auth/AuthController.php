<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegistrationForm()
    {
        $roles = \App\Models\Role::all();
        return view('auth.register', ['roles' => $roles]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'Email' => 'required|email',
            'Contrasena' => 'required'
        ]);

        // Buscar usuario manualmente y verificar contraseña, incluyendo la relación role
        $user = User::with('role')->where('Email', $credentials['Email'])->first();
        
        if ($user && Hash::check($credentials['Contrasena'], $user->Contrasena)) {
            auth()->login($user);
            $request->session()->regenerate();
            
            // Obtener el rol del usuario (ya cargado con la relación)
            $user = auth()->user();
            // Asegurar que la relación esté cargada
            if (!$user->relationLoaded('role')) {
                $user->load('role');
            }
            $rol = $user->role ? $user->role->NombreRol : null;

            // Redireccionar según el rol
            switch ($rol) {
                case 'Administrador':
                    return redirect()->route('admin.dashboard');
                case 'Propietario':
                    return redirect()->route('owner.dashboard');
                case 'Empleado':
                    return redirect()->route('employee.dashboard');
                default:
                    return redirect()->route('dashboard');
            }
        }

        return back()->withErrors([
            'Email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->withInput($request->except('Contrasena'));
    }

    public function register(Request $request)
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
            'Telefono.max' => 'El teléfono no puede tener más de 20 caracteres.',
            'FechaNacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'IDRol.required' => 'Debe seleccionar un rol.',
            'IDRol.exists' => 'El rol seleccionado no es válido.',
            'Contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'Contrasena.confirmed' => 'Las contraseñas no coinciden.'
        ];

        $validated = $request->validate([
            'TipoIdentificacion' => 'required|string|max:50',
            'NumeroIdentificacion' => 'required|string|max:50|unique:usuarios',
            'Nombre' => 'required|string|max:50',
            'Apellido' => 'required|string|max:50',
            'Email' => 'required|string|email|max:150|unique:usuarios',
            'Telefono' => ['required', 'string', 'regex:/^[+]?([0-9]{8,15})$/', 'max:20'],
            'FechaNacimiento' => 'nullable|date|before:today',
            'Direccion' => 'nullable|string|max:500',
            'IDRol' => 'required|exists:roles,IDRol',
            'Contrasena' => 'required|string|min:8|confirmed'
        ], $messages);

        try {
            // Limpiar el número de teléfono antes de guardarlo
            $telefono = preg_replace('/[^0-9+]/', '', $request->Telefono);

            $user = \App\Models\User::create([
                'TipoIdentificacion' => $request->TipoIdentificacion,
                'NumeroIdentificacion' => $request->NumeroIdentificacion,
                'Nombre' => $request->Nombre,
                'Apellido' => $request->Apellido,
                'Email' => $request->Email,
                'Telefono' => $telefono,
                'FechaNacimiento' => $request->FechaNacimiento,
                'Direccion' => $request->Direccion,
                'Contrasena' => Hash::make($request->Contrasena),
                'IDRol' => $request->IDRol
            ]);

            // Cargar la relación role antes de hacer login
            $user->load('role');
            auth()->login($user);
            
            return redirect('/dashboard');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error al registrar el usuario: ' . $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        // Obtener el nombre del usuario antes de cerrar sesión
        $userName = auth()->user()->Nombre ?? 'Usuario';
        
        // Cerrar sesión
        auth()->logout();
        
        // Invalidar la sesión y regenerar el token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redireccionar con mensaje de éxito
        return redirect('/')->with('success', "¡Hasta luego, {$userName}! Has cerrado sesión correctamente.");
    }
}
