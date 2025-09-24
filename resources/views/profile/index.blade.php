@extends('layouts.app-with-sidebar')

@section('title', 'Mi Perfil')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                    {{ substr($user->Nombre, 0, 1) }}{{ substr($user->Apellido, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $user->Nombre }} {{ $user->Apellido }}</h1>
                    <p class="text-gray-600">{{ $user->role->NombreRol ?? 'Sin rol asignado' }}</p>
                    <p class="text-gray-500">{{ $user->Email }}</p>
                </div>
            </div>
        </div>

        <!-- Information Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Información Personal</h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Tipo de Identificación</label>
                        <p class="text-gray-800">{{ $user->TipoIdentificacion }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Número de Identificación</label>
                        <p class="text-gray-800">{{ $user->NumeroIdentificacion }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Fecha de Nacimiento</label>
                        <p class="text-gray-800">
                            {{ $user->FechaNacimiento ? $user->FechaNacimiento->format('d/m/Y') : 'No especificada' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Teléfono</label>
                        <p class="text-gray-800">{{ $user->Telefono }}</p>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Información de Contacto</h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Correo Electrónico</label>
                        <p class="text-gray-800">{{ $user->Email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Dirección</label>
                        <p class="text-gray-800">{{ $user->Direccion ?? 'No especificada' }}</p>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Información de Cuenta</h2>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Rol</label>
                        <p class="text-gray-800">{{ $user->role->NombreRol ?? 'Sin rol asignado' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Fecha de Registro</label>
                        <p class="text-gray-800">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Última Actualización</label>
                        <p class="text-gray-800">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Acciones</h2>
                <div class="space-y-3">
                    <a href="{{ route('profile.settings') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Editar Perfil
                    </a>
                    <br>
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
