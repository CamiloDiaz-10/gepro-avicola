@extends('layouts.app-with-sidebar')

@section('title', 'Dashboard Administrativo - Gepro Avícola')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8 bg-white rounded-lg shadow-lg p-6">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Dashboard Administrativo</h1>
                    <p class="mt-2 text-sm lg:text-base text-gray-600">Panel de control completo del sistema de gestión avícola</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">Exportar </span>Reportes
                    </a>
                    <a href="{{ route('admin.fincas.create') }}" class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Nueva Finca
                    </a>
                </div>
            </div>
        </div>

        <!-- Overview Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Total Usuarios</p>
                        <p class="text-3xl font-bold">{{ $statistics['overview']['total_users'] }}</p>
                    </div>
                    <i class="fas fa-users fa-2x text-blue-200"></i>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Fincas Activas</p>
                        <p class="text-3xl font-bold">{{ $statistics['overview']['total_farms'] }}</p>
                    </div>
                    <i class="fas fa-map-marker-alt fa-2x text-green-200"></i>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Total Aves</p>
                        <p class="text-3xl font-bold">{{ $statistics['overview']['total_birds'] }}</p>
                    </div>
                    <i class="fas fa-feather fa-2x text-purple-200"></i>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm">Producción Hoy</p>
                        <p class="text-3xl font-bold">{{ $statistics['overview']['today_production'] }}</p>
                    </div>
                    <i class="fas fa-egg fa-2x text-yellow-200"></i>
                </div>
            </div>
        </div>

        <!-- Management Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- User Management -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Gestión de Usuarios</h3>
                    <a class="text-blue-600 hover:text-blue-800" href="{{ route('admin.users.index') }}" title="Ir a Gestión de Usuarios">
                        <i class="fas fa-cog"></i>
                    </a>
                </div>
                
                <div class="space-y-3">
                    @foreach($statistics['users']['by_role'] as $role)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                        <span class="font-medium">{{ $role->role }}</span>
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">{{ $role->total }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Usuarios Activos (30 días)</span>
                        <span class="font-semibold">{{ $statistics['users']['active_users'] }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.users.create') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition-colors text-center">
                        <i class="fas fa-user-plus mr-2"></i>Crear Usuario
                    </a>
                </div>
            </div>

            <!-- Farm Management -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Gestión de Fincas</h3>
                    <a class="text-green-600 hover:text-green-800" href="{{ route('admin.fincas.index') }}" title="Ir a Gestión de Fincas">
                        <i class="fas fa-cog"></i>
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach($statistics['farms']['by_location'] as $location)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                        <span class="font-medium">{{ $location->Ubicacion }}</span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">{{ $location->total }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Fincas con Lotes</span>
                        <span class="font-semibold">{{ $statistics['farms']['with_lots'] }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.fincas.create') }}" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded transition-colors">
                        <i class="fas fa-plus mr-2"></i>Nueva Finca
                    </a>
                </div>
            </div>

            <!-- Bird Management -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Gestión de Aves</h3>
                    <a class="text-purple-600 hover:text-purple-800" href="{{ route('admin.lotes.index') }}" title="Ir a Gestión de Aves">
                        <i class="fas fa-cog"></i>
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach($statistics['birds']['by_type'] as $type)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                        <span class="font-medium">{{ $type->type }}</span>
                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-sm">{{ $type->total }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.lotes.create') }}" class="block w-full text-center bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded transition-colors">
                        <i class="fas fa-feather mr-2"></i>Nuevo Lote
                    </a>
                </div>
            </div>
        </div>

        <!-- Production and Health Dashboard -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Production Chart -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Producción de Huevos (7 días)</h3>
                <div class="h-64">
                    <canvas id="productionChart"></canvas>
                </div>
                
                <div class="mt-4">
                    <h4 class="font-semibold mb-2">Producción por Turno Hoy</h4>
                    <div class="grid grid-cols-3 gap-4">
                        @foreach($statistics['production']['by_quality'] as $quality)
                        <div class="text-center p-2 bg-gray-50 rounded">
                            <div class="font-semibold">{{ $quality->quality ?? 'Sin turno' }}</div>
                            <div>{{ $quality->total }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Health Status -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Estado de Salud</h3>
                
                <div class="space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-heartbeat text-blue-600 mr-3"></i>
                            <div>
                                <p class="font-semibold text-blue-800">Actividad Sanitaria</p>
                                <p class="text-blue-600">{{ $statistics['overview']['pending_health_alerts'] }} tratamientos este mes</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-700">Tratamientos Recientes</h4>
                        @foreach($statistics['health']['treatments'] as $treatment)
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-sm">{{ $treatment->treatment }}</span>
                            <span class="text-sm font-semibold">{{ $treatment->total }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Costo Sanitario Estimado (30 días)</span>
                            <span class="font-semibold">${{ number_format($statistics['health']['estimated_cost_30d'] ?? 0) }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('admin.sanidad.create') }}" class="block w-full text-center bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded transition-colors">
                            <i class="fas fa-heartbeat mr-2"></i>Registrar Tratamiento
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial and Feeding Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Financial Summary -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen Financiero (30 días)</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                        <div>
                            <p class="text-sm text-blue-600">Ingresos (estimados)</p>
                            <p class="text-2xl font-bold text-blue-800">${{ number_format($statistics['financial']['revenue']) }}</p>
                        </div>
                        <i class="fas fa-arrow-up text-blue-600 fa-2x"></i>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-yellow-50 rounded-lg">
                        <div>
                            <p class="text-sm text-yellow-700">Gastos (estimados)</p>
                            <p class="text-2xl font-bold text-yellow-800">${{ number_format($statistics['financial']['estimated_expenses_30d']) }}</p>
                        </div>
                        <i class="fas fa-arrow-down text-yellow-600 fa-2x"></i>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg">
                        <div>
                            <p class="text-sm text-green-600">Neto (30 días)</p>
                            <p class="text-2xl font-bold text-green-800">${{ number_format($statistics['financial']['estimated_net_30d']) }}</p>
                        </div>
                        <i class="fas fa-wallet text-green-600 fa-2x"></i>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-gray-50 rounded">
                            <p class="text-xs text-gray-500">Movimientos de Venta</p>
                            <p class="text-lg font-semibold text-gray-800">{{ number_format($statistics['financial']['sales']) }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <p class="text-xs text-gray-500">Movimientos de Compra</p>
                            <p class="text-lg font-semibold text-gray-800">{{ number_format($statistics['financial']['purchases']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feeding Management -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Gestión de Alimentación</h3>
                
                <div class="space-y-4">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-yellow-600 mr-3"></i>
                            <div>
                                <p class="font-semibold text-yellow-800">Stock Bajo</p>
                                <p class="text-yellow-600">{{ $statistics['overview']['low_stock_feeds'] }} tipos de alimento con stock bajo</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-700">Consumo por Tipo (30 días)</h4>
                        @foreach($statistics['feeding']['by_feed_type'] as $feed)
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-sm">{{ $feed->feed_type }}</span>
                            <span class="text-sm font-semibold">{{ number_format($feed->total) }} kg</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Costo Alimentación (7 días)</span>
                            <span class="font-semibold">${{ number_format($statistics['feeding']['estimated_costs']['total_cost_7d'] ?? 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Costo Alimentación (30 días)</span>
                            <span class="font-semibold">${{ number_format($statistics['feeding']['estimated_costs']['total_cost_30d'] ?? 0) }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Precio por kg (config)</span>
                            <span>${{ number_format($statistics['feeding']['estimated_costs']['price_per_kg'] ?? 0) }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded transition-colors">
                            <i class="fas fa-utensils mr-2"></i>Registrar Alimentación
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actividades Recientes</h3>
            
            <div class="space-y-3">
                @foreach($statistics['recent_activities'] as $activity)
                <div class="flex items-center p-3 border border-gray-200 rounded-lg">
                    @if($activity->type == 'user_registration')
                        <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium">Nuevo usuario registrado: {{ $activity->Nombre }} {{ $activity->Apellido }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                        </div>
                    @elseif($activity->type == 'lot_movement')
                        <i class="fas fa-exchange-alt text-green-600 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm font-medium">{{ $activity->TipoMovimiento }} en lote: {{ $activity->Nombre }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>

            @if(empty($statistics['recent_activities']))
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox fa-3x mb-4"></i>
                <p>No hay actividades recientes</p>
            </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.users.index') }}" class="bg-white hover:bg-gray-50 border-2 border-blue-200 text-blue-700 p-4 rounded-lg transition-colors text-center block">
                <i class="fas fa-users fa-2x mb-2"></i>
                <p class="font-semibold">Gestionar Usuarios</p>
            </a>
            
            <a href="{{ route('admin.fincas.index') }}" class="bg-white hover:bg-gray-50 border-2 border-green-200 text-green-700 p-4 rounded-lg transition-colors text-center block">
                <i class="fas fa-map-marker-alt fa-2x mb-2"></i>
                <p class="font-semibold">Gestionar Fincas</p>
            </a>
            
            <a href="{{ route('admin.lotes.index') }}" class="bg-white hover:bg-gray-50 border-2 border-purple-200 text-purple-700 p-4 rounded-lg transition-colors text-center block">
                <i class="fas fa-feather fa-2x mb-2"></i>
                <p class="font-semibold">Gestionar Lotes</p>
            </a>
            
            <a href="{{ route('admin.reports.index') }}" class="bg-white hover:bg-gray-50 border-2 border-red-200 text-red-700 p-4 rounded-lg transition-colors text-center block">
                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                <p class="font-semibold">Ver Reportes</p>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Production Chart
    const ctx = document.getElementById('productionChart').getContext('2d');
    const productionData = @json($statistics['production']['daily']);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: productionData.map(day => {
                const date = new Date(day.date);
                return date.toLocaleDateString('es-ES', { month: 'short', day: 'numeric' });
            }),
            datasets: [{
                label: 'Huevos Producidos',
                data: productionData.map(day => day.total),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Management Functions
    function showUserManagement() {
        alert('Redirigiendo a gestión de usuarios...');
        // window.location.href = '/admin/users';
    }

    function showFarmManagement() {
        alert('Redirigiendo a gestión de fincas...');
        // window.location.href = '/admin/farms';
    }

    function showBirdManagement() {
        alert('Redirigiendo a gestión de aves...');
        // window.location.href = '/admin/birds';
    }
</script>
@endpush
