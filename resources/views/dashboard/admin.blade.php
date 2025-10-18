@extends('layouts.app-with-sidebar')

@section('title', 'Dashboard Administrador - Gepro Avícola')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Panel de Administración</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Bienvenido, {{ auth()->user()->Nombre }} {{ auth()->user()->Apellido }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    Administrador
                </span>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-600 bg-opacity-75">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Usuarios Totales</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $statistics['users']['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-600 bg-opacity-75">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Nuevos Usuarios</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $statistics['users']['newUsers'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Acciones Administrativas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="#" class="flex items-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30">
                    <i class="fas fa-users text-indigo-600 dark:text-indigo-400 text-xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-indigo-900 dark:text-indigo-300">Gestionar Usuarios</h3>
                        <p class="text-sm text-indigo-700 dark:text-indigo-400">Administrar usuarios y permisos</p>
                    </div>
                </a>

                <a href="#" class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30">
                    <i class="fas fa-user-shield text-green-600 dark:text-green-400 text-xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-green-900 dark:text-green-300">Gestionar Roles</h3>
                        <p class="text-sm text-green-700 dark:text-green-400">Configurar roles y permisos</p>
                    </div>
                </a>

                <a href="#" class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30">
                    <i class="fas fa-clipboard-list text-purple-600 dark:text-purple-400 text-xl mr-3"></i>
                    <div>
                        <h3 class="font-semibold text-purple-900 dark:text-purple-300">Registros del Sistema</h3>
                        <p class="text-sm text-purple-700 dark:text-purple-400">Ver logs y actividad</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection