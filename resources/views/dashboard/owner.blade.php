@extends('layouts.app-with-sidebar')

@section('title', 'Dashboard Propietario - Gepro Avícola')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
    <!-- Header con botón de logout -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">Panel de Propietario</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">Bienvenido, {{ auth()->user()->Nombre }} {{ auth()->user()->Apellido }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs font-medium px-2.5 py-0.5 rounded-full">
                Propietario
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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-600 bg-opacity-75">
                    <i class="fas fa-home text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Mis Fincas</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $statistics['farms']['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-600 bg-opacity-75">
                    <i class="fas fa-layer-group text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Total Lotes</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $statistics['lots']['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-600 bg-opacity-75">
                    <i class="fas fa-feather text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Total Aves</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $statistics['birds']['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-600 bg-opacity-75">
                    <i class="fas fa-egg text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Producción Hoy</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $statistics['eggProduction']['today'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fincas Asignadas -->
    @if(isset($statistics['farms']['list']) && $statistics['farms']['list']->isNotEmpty())
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-map-marked-alt text-blue-600 dark:text-blue-400 mr-2"></i>
                    Mis Fincas Asignadas
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Tienes acceso a {{ $statistics['farms']['total'] }} {{ $statistics['farms']['total'] == 1 ? 'finca' : 'fincas' }}
                </p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($statistics['farms']['list'] as $finca)
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-600 bg-opacity-75 rounded-full mr-3">
                                    <i class="fas fa-warehouse text-white text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $finca->Nombre }}</h3>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                        <i class="fas fa-check-circle mr-1"></i> Acceso Activo
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center text-gray-700 dark:text-gray-300">
                                <i class="fas fa-map-marker-alt w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="ml-2">{{ $finca->Ubicacion }}</span>
                            </div>
                            @if($finca->Hectareas)
                            <div class="flex items-center text-gray-700 dark:text-gray-300">
                                <i class="fas fa-ruler-combined w-5 text-gray-500 dark:text-gray-400"></i>
                                <span class="ml-2">{{ number_format($finca->Hectareas, 2) }} hectáreas</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Sin fincas asignadas -->
    <div class="mb-6">
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-6 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-yellow-800 dark:text-yellow-300">
                        Sin Fincas Asignadas
                    </h3>
                    <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">
                        Actualmente no tienes fincas asignadas. Contacta al administrador del sistema para solicitar acceso a una o más fincas.
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Actions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Farm Management -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Gestión de Fincas</h2>
                <div class="space-y-4">
                    <a href="#" class="block p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30">
                        <div class="flex items-center">
                            <i class="fas fa-list text-blue-600 dark:text-blue-400 text-xl mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-blue-900 dark:text-blue-300">Ver Mis Fincas</h3>
                                <p class="text-sm text-blue-700 dark:text-blue-400">Administrar fincas y lotes</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="#" class="block p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30">
                        <div class="flex items-center">
                            <i class="fas fa-feather text-green-600 dark:text-green-400 text-xl mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-green-900 dark:text-green-300">Gestión de Aves</h3>
                                <p class="text-sm text-green-700 dark:text-green-400">Control y seguimiento de aves</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Production Reports -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Informes y Reportes</h2>
                <div class="space-y-4">
                    <a href="#" class="block p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30">
                        <div class="flex items-center">
                            <i class="fas fa-chart-bar text-purple-600 dark:text-purple-400 text-xl mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-purple-900 dark:text-purple-300">Reportes de Producción</h3>
                                <p class="text-sm text-purple-700 dark:text-purple-400">Ver estadísticas y análisis</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="block p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-600 dark:text-red-400 text-xl mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-red-900 dark:text-red-300">Exportar Informes</h3>
                                <p class="text-sm text-red-700 dark:text-red-400">Generar reportes en PDF</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection