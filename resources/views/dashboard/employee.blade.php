@extends('layouts.app-with-sidebar')

@section('title', 'Dashboard Empleado - Gepro Avícola')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
    <!-- Header con botón de logout -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Panel de Empleado</h1>
            <p class="text-gray-600 mt-1">Bienvenido, {{ auth()->user()->Nombre }} {{ auth()->user()->Apellido }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                Empleado
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
        <!-- Assigned Farms Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <i class="fas fa-warehouse text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Fincas Asignadas</h3>
                        <p class="mt-1 text-3xl font-semibold text-blue-600">
                            {{ $statistics['assignedFarms'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Tasks Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <i class="fas fa-tasks text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Tareas de Hoy</h3>
                        <p class="mt-1 text-3xl font-semibold text-green-600">
                            {{ $statistics['todayTasks'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Reports Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <i class="fas fa-clipboard-list text-white text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Reportes Pendientes</h3>
                        <p class="mt-1 text-3xl font-semibold text-yellow-600">
                            {{ $statistics['pendingReports'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Acciones Rápidas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Production Records -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Registro de Producción</h3>
                    <div class="space-y-4">
                        <a href="#" class="block p-4 bg-blue-50 rounded-lg hover:bg-blue-100">
                            <div class="flex items-center">
                                <i class="fas fa-plus-circle text-blue-600 text-xl mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-blue-900">Nuevo Registro</h4>
                                    <p class="text-sm text-blue-700">Agregar producción diaria</p>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="flex items-center">
                                <i class="fas fa-history text-gray-600 text-xl mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Ver Historial</h4>
                                    <p class="text-sm text-gray-700">Consultar registros anteriores</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Health Records -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Registro de Salud</h3>
                    <div class="space-y-4">
                        <a href="#" class="block p-4 bg-green-50 rounded-lg hover:bg-green-100">
                            <div class="flex items-center">
                                <i class="fas fa-notes-medical text-green-600 text-xl mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-green-900">Nuevo Reporte</h4>
                                    <p class="text-sm text-green-700">Registrar control sanitario</p>
                                </div>
                            </div>
                        </a>
                        <a href="#" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                            <div class="flex items-center">
                                <i class="fas fa-clipboard-check text-gray-600 text-xl mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-900">Ver Reportes</h4>
                                    <p class="text-sm text-gray-700">Consultar historial sanitario</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection