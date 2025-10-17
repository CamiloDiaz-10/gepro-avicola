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
        <div class="glass-effect p-8 rounded-2xl w-full max-w-3xl animate-fade-in" style="max-height: 90vh; overflow-y: auto;">
            <!-- Logo y Título -->
            <div class="text-center mb-6">
                <img src="{{ asset('images/logo.jpg') }}" alt="Gepro Avícola" class="h-20 w-20 mx-auto mb-4 rounded-full logo-shadow object-cover">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Crear Cuenta</h2>
                <p class="text-gray-600">Únete a Gepro Avícola</p>
            </div>

            @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Indicador de progreso visual -->
                <div class="progress-indicator">
                    <div class="progress-step active"></div>
                    <div class="progress-step active"></div>
                    <div class="progress-step active"></div>
                    <div class="progress-step active"></div>
                </div>

                <!-- SECCIÓN 1: Identificación -->
                <div class="form-section">
                    <div class="form-section-title">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                        Información de Identificación
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="TipoIdentificacion" class="block text-gray-700 font-semibold mb-2">
                                Tipo de Identificación
                                <span class="required-badge">REQUERIDO</span>
                            </label>
                            <div class="input-with-icon">
                                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <select name="TipoIdentificacion" id="TipoIdentificacion" required>
                                    <option value="">Seleccione tipo</option>
                                    <option value="CC" {{ old('TipoIdentificacion') == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                    <option value="CE" {{ old('TipoIdentificacion') == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                    <option value="NIT" {{ old('TipoIdentificacion') == 'NIT' ? 'selected' : '' }}>NIT</option>
                                    <option value="TI" {{ old('TipoIdentificacion') == 'TI' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                                    <option value="PP" {{ old('TipoIdentificacion') == 'PP' ? 'selected' : '' }}>Pasaporte</option>
                                </select>
                            </div>
                            @error('TipoIdentificacion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="NumeroIdentificacion" class="block text-gray-700 font-semibold mb-2">
                                Número de Identificación
                                <span class="required-badge">REQUERIDO</span>
                            </label>
                            <div class="input-with-icon">
                                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                </svg>
                                <input type="text" name="NumeroIdentificacion" id="NumeroIdentificacion" required 
                                       value="{{ old('NumeroIdentificacion') }}"
                                       placeholder="Ej: 1234567890">
                            </div>
                            @error('NumeroIdentificacion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 2: Información Personal -->
                <div class="form-section">
                    <div class="form-section-title">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Datos Personales
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="Nombre" class="block text-gray-700 font-semibold mb-2">
                                Nombre
                                <span class="required-badge">REQUERIDO</span>
                            </label>
                            <div class="input-with-icon">
                                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <input type="text" name="Nombre" id="Nombre" required 
                                       value="{{ old('Nombre') }}"
                                       placeholder="Tu nombre">
                            </div>
                        </div>

                        <div>
                            <label for="Apellido" class="block text-gray-700 font-semibold mb-2">
                                Apellido
                                <span class="required-badge">REQUERIDO</span>
                            </label>
                            <div class="input-with-icon">
                                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <input type="text" name="Apellido" id="Apellido" required 
                                       value="{{ old('Apellido') }}"
                                       placeholder="Tu apellido">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="Email" class="block text-gray-700 font-semibold mb-2">
                            Correo Electrónico
                            <span class="required-badge">REQUERIDO</span>
                        </label>
                        <div class="input-with-icon">
                            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <input type="email" name="Email" id="Email" required 
                                   value="{{ old('Email') }}"
                                   placeholder="correo@ejemplo.com">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="Telefono" class="block text-gray-700 font-semibold mb-2">
                                Teléfono
                                <span class="required-badge">REQUERIDO</span>
                            </label>
                            <div class="input-with-icon">
                                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <input type="tel" name="Telefono" id="Telefono" required 
                                       pattern="[+]?[0-9]{8,15}"
                                       value="{{ old('Telefono') }}"
                                       placeholder="3001234567">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Solo números (8-15 dígitos)</p>
                            @error('Telefono')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="FechaNacimiento" class="block text-gray-700 font-semibold mb-2">
                                Fecha de Nacimiento
                                <span class="text-xs text-gray-500">(Opcional)</span>
                            </label>
                            <div class="input-with-icon">
                                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <input type="date" name="FechaNacimiento" id="FechaNacimiento" 
                                       value="{{ old('FechaNacimiento') }}"
                                       max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                            </div>
                            @error('FechaNacimiento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN 3: Ubicación y Rol -->
                <div class="form-section">
                    <div class="form-section-title">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Ubicación y Rol en el Sistema
                    </div>
                    
                    <div class="mb-4">
                        <label for="Direccion" class="block text-gray-700 font-semibold mb-2">
                            Dirección
                            <span class="text-xs text-gray-500">(Opcional)</span>
                        </label>
                        <div class="input-with-icon">
                            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="top: 1.5rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <textarea name="Direccion" id="Direccion" rows="2" 
                                      placeholder="Calle, número, ciudad, departamento">{{ old('Direccion') }}</textarea>
                        </div>
                        @error('Direccion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="IDRol" class="block text-gray-700 font-semibold mb-2">
                            Rol en el Sistema
                            <span class="required-badge">REQUERIDO</span>
                        </label>
                        <div class="input-with-icon">
                            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <select name="IDRol" id="IDRol" required>
                                <option value="">Seleccione su rol</option>
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
                        </div>
                        @error('IDRol')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- SECCIÓN 4: Seguridad -->
                <div class="form-section">
                    <div class="form-section-title">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Seguridad de la Cuenta
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="Contrasena" class="block text-gray-700 font-semibold mb-2">
                                Contraseña
                                <span class="required-badge">REQUERIDO</span>
                            </label>
                            <div class="input-with-icon">
                                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                <input type="password" name="Contrasena" id="Contrasena" required 
                                       placeholder="Mínimo 8 caracteres">
                            </div>
                        </div>

                        <div>
                            <label for="Contrasena_confirmation" class="block text-gray-700 font-semibold mb-2">
                                Confirmar Contraseña
                                <span class="required-badge">REQUERIDO</span>
                            </label>
                            <div class="input-with-icon">
                                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <input type="password" name="Contrasena_confirmation" id="Contrasena_confirmation" required 
                                       placeholder="Repite tu contraseña">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs text-blue-800 flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Tu contraseña debe tener al menos 8 caracteres para mayor seguridad.</span>
                        </p>
                    </div>
                </div>

                <!-- Botón de Registro -->
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full py-4 px-6 border border-transparent rounded-lg shadow-lg text-lg font-bold text-white btn-gradient focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Crear Mi Cuenta
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center border-t border-gray-200 pt-6">
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
