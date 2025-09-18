@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-semibold text-gray-900">Reportes</h1>
        
        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Production Report Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Reporte de Producción</h3>
                            <p class="mt-1 text-sm text-gray-500">Ver estadísticas de producción de huevos</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('reports.production') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                            Generar Reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Inventory Report Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <i class="fas fa-warehouse text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Reporte de Inventario</h3>
                            <p class="mt-1 text-sm text-gray-500">Estado actual de aves por lote</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('reports.inventory') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Generar Reporte
                        </a>
                    </div>
                </div>
            </div>

            <!-- Health Report Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <i class="fas fa-heartbeat text-white text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Reporte de Salud</h3>
                            <p class="mt-1 text-sm text-gray-500">Historial de salud y vacunación</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('reports.health') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            Generar Reporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection