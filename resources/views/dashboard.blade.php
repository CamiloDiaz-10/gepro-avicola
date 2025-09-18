@extends('layouts.app-with-sidebar')

@section('title', 'Dashboard - Gepro Avícola')


@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-2 text-gray-600">Resumen general del sistema de gestión avícola</p>
        </div>
        
        <!-- General Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-users fa-2x text-blue-600"></i>
                                    </div>
                                    <div class="ml-5">
                                        <h3 class="text-lg font-semibold mb-2">Usuarios</h3>
                                        <p class="text-3xl font-bold text-blue-600">{{ $statistics['users']['total'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-feather fa-2x text-green-600"></i>
                                    </div>
                                    <div class="ml-5">
                                        <h3 class="text-lg font-semibold mb-2">Total Aves</h3>
                                        <p class="text-3xl font-bold text-green-600">{{ $statistics['birds']['total'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-egg fa-2x text-purple-600"></i>
                                    </div>
                                    <div class="ml-5">
                                        <h3 class="text-lg font-semibold mb-2">Producción Hoy</h3>
                                        <p class="text-3xl font-bold text-purple-600">{{ $statistics['eggProduction']['today'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Producción de Huevos -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h2 class="text-xl font-semibold mb-4">Producción de Huevos</h2>
                                <div class="mb-4">
                                    <canvas id="eggProductionChart"></canvas>
                                </div>
                                <div class="mt-4">
                                    <h3 class="font-semibold mb-2">Calidad de Huevos Hoy</h3>
                                    <div class="grid grid-cols-3 gap-4">
                                        @foreach($statistics['eggProduction']['qualityDistribution'] as $quality)
                                        <div class="text-center p-2 bg-gray-50 rounded">
                                            <div class="font-semibold">{{ $quality->quality_grade }}</div>
                                            <div>{{ $quality->total }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Estado de Salud -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h2 class="text-xl font-semibold mb-4">Estado de Salud</h2>
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="font-semibold mb-2">Condiciones Activas</h3>
                                        @foreach($statistics['health']['conditionsSummary'] as $condition)
                                        <div class="flex justify-between items-center mb-2">
                                            <span>{{ $condition->condition }}</span>
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded">{{ $condition->total }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div>
                                        <h3 class="font-semibold mb-2">Tratamientos Recientes</h3>
                                        <p class="text-lg">{{ $statistics['health']['recentTreatments'] }} en los últimos 7 días</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Inventario de Alimentos -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h2 class="text-xl font-semibold mb-4">Inventario de Alimentos</h2>
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="font-semibold mb-2">Stock por Tipo</h3>
                                        @foreach($statistics['inventory']['byType'] as $type)
                                        <div class="flex justify-between items-center mb-2">
                                            <span>{{ $type->feed_type }}</span>
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $type->total_quantity }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-red-600 mb-2">Stock Bajo</h3>
                                        @foreach($statistics['inventory']['lowStock'] as $item)
                                        <div class="bg-red-50 p-2 rounded mb-2">
                                            <div class="font-semibold">{{ $item->feed_type }}</div>
                                            <div class="text-sm">Cantidad: {{ $item->quantity }} {{ $item->unit }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Estado de las Aves -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                                <h2 class="text-xl font-semibold mb-4">Estado de las Aves</h2>
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="font-semibold mb-2">Por Estado</h3>
                                        @foreach($statistics['birds']['byStatus'] as $status)
                                        <div class="flex justify-between items-center mb-2">
                                            <span>{{ $status->status }}</span>
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">{{ $status->total }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div>
                                        <h3 class="font-semibold mb-2">Llegadas Recientes</h3>
                                        <p class="text-lg">{{ $statistics['birds']['recentArrivals'] }} nuevas aves en los últimos 7 días</p>
                                    </div>
                                </div>
                            </div>
                        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Gráfico de producción de huevos
    const ctx = document.getElementById('eggProductionChart').getContext('2d');
    const weeklyProduction = @json($statistics['eggProduction']['weeklyProduction']);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: weeklyProduction.map(day => day.date),
            datasets: [{
                label: 'Producción Diaria',
                data: weeklyProduction.map(day => day.total),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Producción de Huevos - Últimos 7 días'
                }
            }
        }
    });
</script>
@endpush