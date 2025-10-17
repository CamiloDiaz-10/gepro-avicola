<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Gepro Avícola</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tailwindcss/forms@0.3.4/dist/forms.min.css" rel="stylesheet">
    <style>
        /* Fondo con imagen opaca */
        .bg-image {
            background-image: url('{{ asset('images/fondo.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        
        /* Overlay oscuro sobre la imagen */
        .bg-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }
        
        /* Contenedor del formulario */
        .form-container {
            position: relative;
            z-index: 2;
        }
        
        /* Efecto glassmorphism para el formulario */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        /* Estilos para inputs y selects */
        select, input, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            background-color: white;
            transition: all 0.3s ease;
        }
        
        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2310b981' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }
        
        /* Botón con gradiente */
        .btn-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }
        
        /* Animación de entrada */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Logo con sombra */
        .logo-shadow {
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.3));
        }
        
        /* Secciones del formulario */
        .form-section {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(5, 150, 105, 0.05) 100%);
            border-left: 4px solid #10b981;
            padding: 1.5rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .form-section-title {
            color: #047857;
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Input con icono */
        .input-with-icon {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #10b981;
            pointer-events: none;
        }
        
        .input-with-icon input,
        .input-with-icon select,
        .input-with-icon textarea {
            padding-left: 2.75rem;
        }
        
        /* Badge de requerido */
        .required-badge {
            display: inline-block;
            background: #ef4444;
            color: white;
            font-size: 0.625rem;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            margin-left: 0.25rem;
            font-weight: 600;
        }
        
        /* Indicador de progreso */
        .progress-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        
        .progress-step {
            flex: 1;
            height: 4px;
            background: #e5e7eb;
            margin: 0 0.25rem;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .progress-step.active {
            background: #10b981;
        }
    </style>
</head>
<body class="bg-image">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 form-container">
        <div class="glass-effect p-8 rounded-2xl w-full max-w-2xl animate-fade-in">
            <!-- Logo y Título -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.jpg') }}" alt="Gepro Avícola" class="h-20 w-20 mx-auto mb-4 rounded-full logo-shadow object-cover">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Crear Cuenta</h2>
                <p class="text-gray-600">Únete a Gepro Avícola</p>
            </div>

            @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="TipoIdentificacion" class="block text-gray-700 font-semibold mb-2">Tipo de Identificación</label>
                        <select name="TipoIdentificacion" id="TipoIdentificacion" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Seleccione tipo</option>
                            <option value="CC" {{ old('TipoIdentificacion') == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                            <option value="CE" {{ old('TipoIdentificacion') == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                            <option value="NIT" {{ old('TipoIdentificacion') == 'NIT' ? 'selected' : '' }}>NIT</option>
                            <option value="TI" {{ old('TipoIdentificacion') == 'TI' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                            <option value="PP" {{ old('TipoIdentificacion') == 'PP' ? 'selected' : '' }}>Pasaporte</option>
                        </select>
                        @error('TipoIdentificacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="NumeroIdentificacion" class="block text-gray-700 font-semibold mb-2">Número de Identificación</label>
                        <input type="text" name="NumeroIdentificacion" id="NumeroIdentificacion" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               value="{{ old('NumeroIdentificacion') }}"
                               placeholder="Ingrese su número de identificación">
                        @error('NumeroIdentificacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="Nombre" class="block text-gray-700 font-semibold mb-2">Nombre</label>
                        <input type="text" name="Nombre" id="Nombre" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               value="{{ old('Nombre') }}">
                    </div>

                    <div>
                        <label for="Apellido" class="block text-gray-700 font-semibold mb-2">Apellido</label>
                        <input type="text" name="Apellido" id="Apellido" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               value="{{ old('Apellido') }}">
                    </div>
                </div>

                <div>
                    <label for="Email" class="block text-gray-700 font-semibold mb-2">Correo Electrónico</label>
                    <input type="email" name="Email" id="Email" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           value="{{ old('Email') }}">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="Telefono" class="block text-gray-700 font-semibold mb-2">Teléfono</label>
                        <input type="tel" name="Telefono" id="Telefono" required 
                               pattern="[+]?[0-9]{8,15}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               value="{{ old('Telefono') }}"
                               placeholder="Ej: 3001234567">
                        <p class="mt-1 text-sm text-gray-500">Solo números (8-15 dígitos)</p>
                        @error('Telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="FechaNacimiento" class="block text-gray-700 font-semibold mb-2">Fecha de Nacimiento</label>
                        <input type="date" name="FechaNacimiento" id="FechaNacimiento" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               value="{{ old('FechaNacimiento') }}"
                               max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                        @error('FechaNacimiento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="Direccion" class="block text-gray-700 font-semibold mb-2">Dirección</label>
                    <textarea name="Direccion" id="Direccion" rows="2" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                              placeholder="Ingrese su dirección completa">{{ old('Direccion') }}</textarea>
                    @error('Direccion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="IDRol" class="block text-gray-700 font-semibold mb-2">Rol</label>
                    <select name="IDRol" id="IDRol" required 
                            class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccione un rol</option>
                        @if(isset($roles) && count($roles) > 0)
                            @foreach($roles as $rol)
                                @if(strtolower($rol->NombreRol) !== 'administrador')
                                    <option value="{{ $rol->IDRol }}">{{ $rol->NombreRol }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="" disabled>No hay roles disponibles</option>
                        @endif
                    </select>
                    @error('IDRol')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>



                <div>
                    <label for="Contrasena" class="block text-gray-700 font-semibold mb-2">Contraseña</label>
                    <input type="password" name="Contrasena" id="Contrasena" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label for="Contrasena_confirmation" class="block text-gray-700 font-semibold mb-2">Confirmar Contraseña</label>
                    <input type="password" name="Contrasena_confirmation" id="Contrasena_confirmation" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div>
                    <button type="submit" 
                            class="w-full py-3 px-4 border border-transparent rounded-lg shadow-lg text-base font-semibold text-white btn-gradient focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Registrarse
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-700">
                    ¿Ya tienes una cuenta? 
                    <a href="{{ route('login') }}" class="font-semibold text-green-600 hover:text-green-700 transition-colors">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>