@extends('layouts.app-with-sidebar')

@section('title', 'Dashboard Propietario - Gepro Avícola')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
    <!-- Header con botón de logout -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Panel de Propietario</h1>
            <p class="text-gray-600 mt-1">Bienvenido, {{ auth()->user()->Nombre }} {{ auth()->user()->Apellido }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-600 bg-opacity-75">
                    <i class="fas fa-home text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 uppercase">Mis Fincas</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $statistics['farms']['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-600 bg-opacity-75">
                    <i class="fas fa-feather text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 uppercase">Total Aves</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $statistics['birds']['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-600 bg-opacity-75">
                    <i class="fas fa-egg text-white text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 uppercase">Producción Hoy</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $statistics['eggProduction']['today'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Farm Management -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Gestión de Fincas</h2>
                <div class="space-y-4">
                    <a href="#" class="block p-4 bg-blue-50 rounded-lg hover:bg-blue-100">
                        <div class="flex items-center">
                            <i class="fas fa-list text-blue-600 text-xl mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-blue-900">Ver Mis Fincas</h3>
                                <p class="text-sm text-blue-700">Administrar fincas y lotes</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="#" class="block p-4 bg-green-50 rounded-lg hover:bg-green-100">
                        <div class="flex items-center">
                            <i class="fas fa-feather text-green-600 text-xl mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-green-900">Gestión de Aves</h3>
                                <p class="text-sm text-green-700">Control y seguimiento de aves</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Production Reports -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Informes y Reportes</h2>
                <div class="space-y-4">
                    <a href="#" class="block p-4 bg-purple-50 rounded-lg hover:bg-purple-100">
                        <div class="flex items-center">
                            <i class="fas fa-chart-bar text-purple-600 text-xl mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-purple-900">Reportes de Producción</h3>
                                <p class="text-sm text-purple-700">Ver estadísticas y análisis</p>
                            </div>
                        </div>
                    </a>

                    <a href="#" class="block p-4 bg-red-50 rounded-lg hover:bg-red-100">
                        <div class="flex items-center">
                            <i class="fas fa-file-pdf text-red-600 text-xl mr-3"></i>
                            <div>
                                <h3 class="font-semibold text-red-900">Exportar Informes</h3>
                                <p class="text-sm text-red-700">Generar reportes en PDF</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection