@extends('layouts.app')

@section('title', 'Dashboard Administrador - Gepro Avícola')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header con botón de logout -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Panel de Administración</h1>
            <p class="text-gray-600 mt-1">Bienvenido, {{ auth()->user()->Nombre }} {{ auth()->user()->Apellido }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                Administrador
            </span>
            <x-logout-button 
                size="normal"
                confirmMessage="¿Estás seguro de que deseas cerrar sesión como administrador?"
            >
                Cerrar Sesión
            </x-logout-button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-600 bg-opacity-75">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 uppercase">Usuarios Totales</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $statistics['users']['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-600 bg-opacity-75">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 uppercase">Nuevos Usuarios</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $statistics['users']['newUsers'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Acciones Administrativas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="#" class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100">
                    <i class="fas fa-users text-indigo-600 text-xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-indigo-900">Gestionar Usuarios</h3>
                        <p class="text-sm text-indigo-700">Administrar usuarios y permisos</p>
                    </div>
                </a>

                <a href="#" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100">
                    <i class="fas fa-user-shield text-green-600 text-xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-green-900">Gestionar Roles</h3>
                        <p class="text-sm text-green-700">Configurar roles y permisos</p>
                    </div>
                </a>

                <a href="#" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100">
                    <i class="fas fa-clipboard-list text-purple-600 text-xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-purple-900">Registros del Sistema</h3>
                        <p class="text-sm text-purple-700">Ver logs y actividad</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection