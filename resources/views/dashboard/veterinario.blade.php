@extends('layouts.app-with-sidebar')

@section('title', 'Dashboard Veterinario - Gepro Avícola')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
    <!-- Header con botón de logout -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Panel de Veterinario</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">Bienvenido, {{ auth()->user()->Nombre }} {{ auth()->user()->Apellido }}</p>
        </div>

    
        <div class="flex items-center space-x-4">
            <span class="bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 text-xs font-medium px-2.5 py-0.5 rounded-full">
                Veterinario
            </span>
            <x-logout-button 
                size="normal"
                confirmMessage="¿Estás seguro de que deseas cerrar sesión?"
            >
                Cerrar Sesión
            </x-logout-button>
        </div>
    </div>
        
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Alimentación Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <i class="fas fa-drumstick-bite text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Registros de Alimentación</h3>
                        <p class="mt-1 text-3xl font-semibold text-green-600 dark:text-green-400">
                            {{ $statistics['feedingRecords'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lotes Activos Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <i class="fas fa-layer-group text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Lotes Activos</h3>
                        <p class="mt-1 text-3xl font-semibold text-blue-600 dark:text-blue-400">
                            {{ $statistics['activeLots'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consumo Mensual Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Consumo Mensual (Kg)</h3>
                        <p class="mt-1 text-3xl font-semibold text-yellow-600 dark:text-yellow-400">
                            {{ number_format($statistics['monthlyConsumption'] ?? 0, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Acciones Rápidas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('veterinario.alimentacion.index') }}" 
               class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <i class="fas fa-list text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Ver Registros de Alimentación</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Consulta todos los registros</p>
                </div>
            </a>

            <a href="{{ route('veterinario.alimentacion.create') }}" 
               class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition-colors">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <i class="fas fa-plus text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Registrar Alimentación</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Añadir nuevo registro</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Actividad Reciente</h2>
        <div class="space-y-4">
            @if(isset($statistics['recentActivity']) && count($statistics['recentActivity']) > 0)
                @foreach($statistics['recentActivity'] as $activity)
                <div class="flex items-start border-l-4 border-blue-500 pl-4 py-2">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity['description'] ?? 'Actividad' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['date'] ?? 'Fecha no disponible' }}</p>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox fa-3x mb-4"></i>
                    <p>No hay actividad reciente</p>
                </div>
            @endif
        </div>
    </div>
    </div>
</div>
@endsection
