@extends('layouts.app')

@section('title', 'Dashboard - Gepro Avícola')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .active-nav-link { @apply bg-indigo-800 text-white; }
    .nav-link:hover { @apply bg-indigo-700 text-white transition-colors duration-200; }
</style>
@endpush

@section('content')
    <div x-data="{ sidebarOpen: false }" class="min-h-full">
        <!-- Sidebar Móvil -->
        <div x-show="sidebarOpen" class="fixed inset-0 flex z-40 lg:hidden" x-cloak>
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-600 bg-opacity-75"
                 @click="sidebarOpen = false"></div>

            <div x-show="sidebarOpen"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="relative flex-1 flex flex-col max-w-xs w-full bg-indigo-900">

                <div class="absolute top-0 right-0 -mr-12 pt-2">
                    <button @click="sidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                        <span class="sr-only">Cerrar sidebar</span>
                        <i class="fas fa-times text-white"></i>
                    </button>
                </div>

                <!-- Sidebar content -->
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4">
                        <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/workflow-logo-indigo-500-mark-white-text.svg" alt="Gepro Avícola">
                    </div>
                    <nav class="mt-5 px-2 space-y-1">
                        <a href="#" class="active-nav-link group flex items-center px-2 py-2 text-base font-medium rounded-md">
                            <i class="fas fa-home mr-4"></i>
                            Dashboard
                        </a>
                        <a href="#" class="nav-link text-indigo-100 group flex items-center px-2 py-2 text-base font-medium rounded-md">
                            <i class="fas fa-egg mr-4"></i>
                            Producción
                        </a>
                        <a href="#" class="nav-link text-indigo-100 group flex items-center px-2 py-2 text-base font-medium rounded-md">
                            <i class="fas fa-feather mr-4"></i>
                            Gallinas
                        </a>
                        <a href="#" class="nav-link text-indigo-100 group flex items-center px-2 py-2 text-base font-medium rounded-md">
                            <i class="fas fa-warehouse mr-4"></i>
                            Lotes
                        </a>
                        <a href="#" class="nav-link text-indigo-100 group flex items-center px-2 py-2 text-base font-medium rounded-md">
                            <i class="fas fa-chart-bar mr-4"></i>
                            Reportes
                        </a>
                        <a href="#" class="nav-link text-indigo-100 group flex items-center px-2 py-2 text-base font-medium rounded-md">
                            <i class="fas fa-cog mr-4"></i>
                            Configuración
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Sidebar Desktop -->
        <div class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0">
            <div class="flex-1 flex flex-col min-h-0 bg-indigo-900">
                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4">
                        <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/workflow-logo-indigo-500-mark-white-text.svg" alt="Gepro Avícola">
                    </div>
                    <nav class="mt-5 px-2 space-y-1">
                        <a href="#" class="active-nav-link group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-home mr-4"></i>
                            Dashboard
                        </a>
                        <a href="#" class="nav-link text-indigo-100 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-egg mr-4"></i>
                            Producción
                        </a>
                        <a href="#" class="nav-link text-indigo-100 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-feather mr-4"></i>
                            Gallinas
                        </a>
                        <a href="#" class="nav-link text-indigo-100 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-warehouse mr-4"></i>
                            Lotes
                        </a>
                        <a href="#" class="nav-link text-indigo-100 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-chart-bar mr-4"></i>
                            Reportes
                        </a>
                        <a href="#" class="nav-link text-indigo-100 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-cog mr-4"></i>
                            Configuración
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-64 flex flex-col flex-1">
            <!-- Top navigation -->
            <div class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white shadow">
                <button @click="sidebarOpen = true" class="px-4 border-r border-gray-200 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 lg:hidden">
                    <span class="sr-only">Abrir sidebar</span>
                    <i class="fas fa-bars"></i>
                </button>

                <div class="flex-1 px-4 flex justify-between">
                    <div class="flex-1 flex items-center">
                        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
                    </div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <!-- Profile dropdown -->
                        <div x-data="{ open: false }" class="ml-3 relative">
                            <div>
                                <button @click="open = !open" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button">
                                    <span class="sr-only">Abrir menú de usuario</span>
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->Nombre . ' ' . auth()->user()->Apellido) }}&background=6366f1&color=ffffff" alt="{{ auth()->user()->Nombre }}">
                                </button>
                            </div>
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                                 role="menu" 
                                 x-cloak>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Mi Perfil</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Configuración</a>
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Cerrar Sesión</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <main class="flex-1 pb-8">
                <div class="py-12">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
            </main>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
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