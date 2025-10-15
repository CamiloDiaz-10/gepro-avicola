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
        \Log::info('=== INICIO LOGIN REQUEST ===', [
            'method' => $request->method(),
            'url' => $request->url(),
            'has_email' => $request->has('Email'),
            'has_password' => $request->has('Contrasena'),
            'all_input' => $request->except('Contrasena')
        ]);

        try {
            $credentials = $request->validate([
                'Email' => 'required|email',
                'Contrasena' => 'required'
            ]);
            
            \Log::info('Validación exitosa', ['email' => $credentials['Email']]);

            // Buscar usuario manualmente y verificar contraseña, incluyendo la relación role
            $user = User::with('role')->where('Email', $credentials['Email'])->first();
            
            if (!$user) {
                \Log::warning('Usuario no encontrado', ['email' => $credentials['Email']]);
                return back()->withErrors([
                    'Email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
                ])->withInput($request->except('Contrasena'));
            }
            
            \Log::info('Usuario encontrado, verificando contraseña');
            
            if (!Hash::check($credentials['Contrasena'], $user->Contrasena)) {
                \Log::warning('Contraseña incorrecta');
                return back()->withErrors([
                    'Email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
                ])->withInput($request->except('Contrasena'));
            }
            
            \Log::info('Contraseña correcta, verificando estado de la cuenta');

            // Bloquear acceso si el usuario está inactivo
            if (($user->Estado ?? 'Activo') !== 'Activo') {
                \Log::warning('Usuario inactivo intenta iniciar sesión', [
                    'email' => $user->Email,
                    'estado' => $user->Estado,
                ]);
                return back()->withErrors([
                    'Email' => 'Tu cuenta está desactivada. Por favor, comunícate con el administrador para reactivarla.'
                ])->withInput($request->except('Contrasena'));
            }

            // Login sin "remember me" para que la sesión expire al cerrar el navegador
            auth()->login($user, false);
            
            \Log::info('Usuario logueado, auth()->check(): ' . (auth()->check() ? 'true' : 'false'));
            \Log::info('Session ID antes de regenerar: ' . $request->session()->getId());
            
            $request->session()->regenerate();
            
            \Log::info('Session ID después de regenerar: ' . $request->session()->getId());
            \Log::info('Sesión iniciada, auth()->check(): ' . (auth()->check() ? 'true' : 'false'));
            \Log::info('Session data: ' . json_encode($request->session()->all()));
            
            // Obtener el rol del usuario (ya cargado con la relación)
            $user = auth()->user();
            // Asegurar que la relación esté cargada
            if (!$user->relationLoaded('role')) {
                $user->load('role');
            }
            $rol = $user->role ? $user->role->NombreRol : null;

            \Log::info('Login exitoso', [
                'user' => $user->Email,
                'role' => $rol,
                'user_id' => $user->IDUsuario
            ]);

            // Determinar la ruta según el rol
            $redirectRoute = match ($rol) {
                'Administrador' => '/admin/dashboard',
                'Propietario' => '/owner/dashboard',
                'Empleado' => '/employee/dashboard',
                default => '/dashboard'
            };

            \Log::info('Redirigiendo a: ' . $redirectRoute);
            
            // Guardar explícitamente la sesión antes de redirigir
            $request->session()->save();
            
            \Log::info('Sesión guardada, verificando en DB...');
            $sessionCount = \DB::table('sessions')->where('user_id', $user->IDUsuario)->count();
            \Log::info('Sesiones en DB para usuario: ' . $sessionCount);
            
            // Redirección directa
            return redirect($redirectRoute)->with('login_success', true);
            
        } catch (\Exception $e) {
            \Log::error('Error en login: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors([
                'Email' => 'Error al procesar el login: ' . $e->getMessage(),
            ])->withInput($request->except('Contrasena'));
        }
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
            // Login sin "remember me" para que la sesión expire al cerrar el navegador
            auth()->login($user, false);
            
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
