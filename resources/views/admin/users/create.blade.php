@extends('layouts.app-with-sidebar')

@section('title', 'Crear Usuario - Gepro Avícola')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Crear Nuevo Usuario</h1>
                    <p class="mt-2 text-gray-600">Completa la información para crear un nuevo usuario</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf

            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Personal</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tipo de Identificación -->
                    <div>
                        <label for="TipoIdentificacion" class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Identificación <span class="text-red-500">*</span>
                        </label>
                        <select name="TipoIdentificacion" id="TipoIdentificacion" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('TipoIdentificacion') border-red-500 @enderror">
                            <option value="">Selecciona un tipo</option>
                            <option value="CC" {{ old('TipoIdentificacion') === 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                            <option value="CE" {{ old('TipoIdentificacion') === 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                            <option value="TI" {{ old('TipoIdentificacion') === 'TI' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                            <option value="PP" {{ old('TipoIdentificacion') === 'PP' ? 'selected' : '' }}>Pasaporte</option>
                        </select>
                        @error('TipoIdentificacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Número de Identificación -->
                    <div>
                        <label for="NumeroIdentificacion" class="block text-sm font-medium text-gray-700 mb-2">
                            Número de Identificación <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="NumeroIdentificacion" id="NumeroIdentificacion" 
                               value="{{ old('NumeroIdentificacion') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('NumeroIdentificacion') border-red-500 @enderror">
                        @error('NumeroIdentificacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nombre -->
                    <div>
                        <label for="Nombre" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="Nombre" id="Nombre" 
                               value="{{ old('Nombre') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('Nombre') border-red-500 @enderror">
                        @error('Nombre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Apellido -->
                    <div>
                        <label for="Apellido" class="block text-sm font-medium text-gray-700 mb-2">
                            Apellido <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="Apellido" id="Apellido" 
                               value="{{ old('Apellido') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('Apellido') border-red-500 @enderror">
                        @error('Apellido')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="Email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="Email" id="Email" 
                               value="{{ old('Email') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('Email') border-red-500 @enderror">
                        @error('Email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="Telefono" class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                        <input type="tel" name="Telefono" id="Telefono" 
                               value="{{ old('Telefono') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('Telefono') border-red-500 @enderror">
                        @error('Telefono')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div>
                        <label for="FechaNacimiento" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Nacimiento</label>
                        <input type="date" name="FechaNacimiento" id="FechaNacimiento" 
                               value="{{ old('FechaNacimiento') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('FechaNacimiento') border-red-500 @enderror">
                        @error('FechaNacimiento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rol -->
                    <div>
                        <label for="IDRol" class="block text-sm font-medium text-gray-700 mb-2">
                            Rol <span class="text-red-500">*</span>
                        </label>
                        <select name="IDRol" id="IDRol" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('IDRol') border-red-500 @enderror">
                            <option value="">Selecciona un rol</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->IDRol }}" {{ old('IDRol') == $role->IDRol ? 'selected' : '' }}>
                                {{ $role->NombreRol }}
                            </option>
                            @endforeach
                        </select>
                        @error('IDRol')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Dirección -->
                <div class="mt-6">
                    <label for="Direccion" class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                    <textarea name="Direccion" id="Direccion" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('Direccion') border-red-500 @enderror">{{ old('Direccion') }}</textarea>
                    @error('Direccion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Security Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información de Seguridad</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Contraseña -->
                    <div>
                        <label for="Contrasena" class="block text-sm font-medium text-gray-700 mb-2">
                            Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="Contrasena" id="Contrasena" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('Contrasena') border-red-500 @enderror">
                        @error('Contrasena')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div>
                        <label for="Contrasena_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmar Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="Contrasena_confirmation" id="Contrasena_confirmation" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Farm Assignment -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Asignación de Fincas</h3>
                <p class="text-sm text-gray-600 mb-4">Selecciona las fincas a las que tendrá acceso este usuario</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($fincas as $finca)
                    <div class="flex items-center">
                        <input type="checkbox" name="fincas[]" value="{{ $finca->IDFinca }}" 
                               id="finca_{{ $finca->IDFinca }}"
                               {{ in_array($finca->IDFinca, old('fincas', [])) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="finca_{{ $finca->IDFinca }}" class="ml-2 block text-sm text-gray-900">
                            {{ $finca->NombreFinca }}
                            <span class="text-gray-500 text-xs block">{{ $finca->Ubicacion }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.users.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition-colors">
                    <i class="fas fa-save mr-2"></i>Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
