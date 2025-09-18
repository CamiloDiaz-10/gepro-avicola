<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Gepro Avícola</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tailwindcss/forms@0.3.4/dist/forms.min.css" rel="stylesheet">
    <style>
        select, input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: white;
        }
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Crear Cuenta</h2>

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
                        <label for="TipoIdentificacion" class="block text-gray-700">Tipo de Identificación</label>
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
                        <label for="NumeroIdentificacion" class="block text-gray-700">Número de Identificación</label>
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
                        <label for="Nombre" class="block text-gray-700">Nombre</label>
                        <input type="text" name="Nombre" id="Nombre" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               value="{{ old('Nombre') }}">
                    </div>

                    <div>
                        <label for="Apellido" class="block text-gray-700">Apellido</label>
                        <input type="text" name="Apellido" id="Apellido" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               value="{{ old('Apellido') }}">
                    </div>
                </div>

                <div>
                    <label for="Email" class="block text-gray-700">Correo Electrónico</label>
                    <input type="email" name="Email" id="Email" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           value="{{ old('Email') }}">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="Telefono" class="block text-gray-700">Teléfono</label>
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
                        <label for="FechaNacimiento" class="block text-gray-700">Fecha de Nacimiento</label>
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
                    <label for="Direccion" class="block text-gray-700">Dirección</label>
                    <textarea name="Direccion" id="Direccion" rows="2" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                              placeholder="Ingrese su dirección completa">{{ old('Direccion') }}</textarea>
                    @error('Direccion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="IDRol" class="block text-gray-700">Rol</label>
                    <select name="IDRol" id="IDRol" required 
                            class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Seleccione un rol</option>
                        @if(isset($roles) && count($roles) > 0)
                            @foreach($roles as $rol)
                                <option value="{{ $rol->IDRol }}">{{ $rol->NombreRol }}</option>
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
                    <label for="Contrasena" class="block text-gray-700">Contraseña</label>
                    <input type="password" name="Contrasena" id="Contrasena" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label for="Contrasena_confirmation" class="block text-gray-700">Confirmar Contraseña</label>
                    <input type="password" name="Contrasena_confirmation" id="Contrasena_confirmation" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div>
                    <button type="submit" 
                            class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Registrarse
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">
                    ¿Ya tienes una cuenta? 
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Inicia sesión aquí
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>