@extends('layouts.app-with-sidebar')

@section('title', 'Configuración del Perfil')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Configuración del Perfil</h1>
            <p class="text-gray-600">Actualiza tu información personal y configuración de cuenta</p>
        </div>

        @if (session('status'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Update Profile Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Información Personal</h2>
                
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="TipoIdentificacion" class="block text-gray-700 font-medium">Tipo de Identificación</label>
                            <select name="TipoIdentificacion" id="TipoIdentificacion" required 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Seleccione tipo</option>
                                <option value="CC" {{ old('TipoIdentificacion', $user->TipoIdentificacion) == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                <option value="CE" {{ old('TipoIdentificacion', $user->TipoIdentificacion) == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                <option value="NIT" {{ old('TipoIdentificacion', $user->TipoIdentificacion) == 'NIT' ? 'selected' : '' }}>NIT</option>
                                <option value="TI" {{ old('TipoIdentificacion', $user->TipoIdentificacion) == 'TI' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                                <option value="PP" {{ old('TipoIdentificacion', $user->TipoIdentificacion) == 'PP' ? 'selected' : '' }}>Pasaporte</option>
                            </select>
                            @error('TipoIdentificacion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="NumeroIdentificacion" class="block text-gray-700 font-medium">Número de Identificación</label>
                            <input type="text" name="NumeroIdentificacion" id="NumeroIdentificacion" required 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   value="{{ old('NumeroIdentificacion', $user->NumeroIdentificacion) }}">
                            @error('NumeroIdentificacion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="Nombre" class="block text-gray-700 font-medium">Nombre</label>
                            <input type="text" name="Nombre" id="Nombre" required 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   value="{{ old('Nombre', $user->Nombre) }}">
                            @error('Nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="Apellido" class="block text-gray-700 font-medium">Apellido</label>
                            <input type="text" name="Apellido" id="Apellido" required 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   value="{{ old('Apellido', $user->Apellido) }}">
                            @error('Apellido')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="Email" class="block text-gray-700 font-medium">Correo Electrónico</label>
                        <input type="email" name="Email" id="Email" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                               value="{{ old('Email', $user->Email) }}">
                        @error('Email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="Telefono" class="block text-gray-700 font-medium">Teléfono</label>
                            <input type="tel" name="Telefono" id="Telefono" required 
                                   pattern="[+]?[0-9]{8,15}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   value="{{ old('Telefono', $user->Telefono) }}">
                            @error('Telefono')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="FechaNacimiento" class="block text-gray-700 font-medium">Fecha de Nacimiento</label>
                            <input type="date" name="FechaNacimiento" id="FechaNacimiento" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   value="{{ old('FechaNacimiento', $user->FechaNacimiento ? $user->FechaNacimiento->format('Y-m-d') : '') }}"
                                   max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                            @error('FechaNacimiento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="Direccion" class="block text-gray-700 font-medium">Dirección</label>
                        <textarea name="Direccion" id="Direccion" rows="2" 
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('Direccion', $user->Direccion) }}</textarea>
                        @error('Direccion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Actualizar Perfil
                        </button>
                    </div>
                </form>
            </div>

            <!-- Update Password -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Cambiar Contraseña</h2>
                
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-gray-700 font-medium">Contraseña Actual</label>
                        <input type="password" name="current_password" id="current_password" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-gray-700 font-medium">Nueva Contraseña</label>
                        <input type="password" name="password" id="password" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-gray-700 font-medium">Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <button type="submit" 
                                class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Cambiar Contraseña
                        </button>
                    </div>
                </form>

                <!-- Account Information -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-800 mb-3">Información de Cuenta</h3>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><strong>Rol:</strong> {{ $user->role->NombreRol ?? 'Sin rol asignado' }}</p>
                        <p><strong>Fecha de Registro:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Última Actualización:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="mt-6 text-center">
            <a href="{{ route('profile.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors mr-4">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al Perfil
            </a>
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                <i class="fas fa-tachometer-alt mr-2"></i>
                Ir al Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
