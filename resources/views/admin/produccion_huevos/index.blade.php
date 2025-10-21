@extends('layouts.app-with-sidebar')

@section('title', 'Reporte de Producción de Huevos')

@section('content')
<div class="p-6" x-data>
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            @php
                $current = Route::currentRouteName();
                $area = \Illuminate\Support\Str::startsWith($current, 'owner.') ? 'owner' : (\Illuminate\Support\Str::startsWith($current, 'employee.') ? 'employee' : 'admin');
            @endphp
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Producción de Huevos</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Filtra por fechas, lote y turno</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route($area.'.produccion-huevos.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Registrar Producción
                    </a>
                    <a href="{{ route($area.'.produccion-huevos.export.csv', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500">
                        Exportar CSV
                    </a>
                </div>
            </div>

            <form method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Desde</label>
                    <input type="date" name="from" value="{{ $filters['from'] }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Hasta</label>
                    <input type="date" name="to" value="{{ $filters['to'] }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Lote</label>
                    <select name="lote" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Todos</option>
                        @foreach($lotes as $lote)
                            <option value="{{ $lote->IDLote }}" {{ $filters['lote'] == $lote->IDLote ? 'selected' : '' }}>{{ $lote->Nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Turno</label>
                    <select name="turno" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Todos</option>
                        <option value="Mañana" {{ $filters['turno'] == 'Mañana' ? 'selected' : '' }}>Mañana</option>
                        <option value="Tarde" {{ $filters['turno'] == 'Tarde' ? 'selected' : '' }}>Tarde</option>
                        <option value="Noche" {{ $filters['turno'] == 'Noche' ? 'selected' : '' }}>Noche</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500">Aplicar</button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-gray-500 dark:text-gray-400 text-sm">Total huevos</div>
                <div class="text-3xl font-bold dark:text-white">{{ number_format($totales->total_huevos ?? 0) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-gray-500 dark:text-gray-400 text-sm">Huevos rotos</div>
                <div class="text-3xl font-bold dark:text-white">{{ number_format($totales->total_rotos ?? 0) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-gray-500 dark:text-gray-400 text-sm">% rotos</div>
                <div class="text-3xl font-bold dark:text-white">{{ number_format($porcentajeRotos, 2) }}%</div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-gray-500 dark:text-gray-400 text-sm">Días analizados</div>
                <div class="text-3xl font-bold dark:text-white">{{ $serieDiaria->count() }}</div>
            </div>
        </div>

        <!-- Estadísticas por Lote: Mejor y Peor Producción -->
        @if($mejorLote && $peorLote)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Lote con MAYOR Producción -->
            @if($mejorLote)
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 shadow-lg rounded-lg p-6 border-2 border-green-300 dark:border-green-700">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-trophy text-green-600 dark:text-green-400 text-2xl"></i>
                            <h2 class="text-xl font-bold text-green-800 dark:text-green-300">Lote con Mayor Producción</h2>
                        </div>
                        <p class="text-sm text-green-600 dark:text-green-400">En el período seleccionado</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $mejorLote->Nombre }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $mejorLote->finca->Nombre ?? 'Sin finca' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                                    {{ number_format($mejorLote->total_producido) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">huevos totales</div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-center">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Promedio Diario</div>
                                <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($mejorLote->promedio_diario, 1) }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Días con Registro</div>
                                <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ $mejorLote->dias_registrados }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 text-sm text-green-700 dark:text-green-400">
                        <i class="fas fa-check-circle"></i>
                        <span>Este lote está generando la mayor producción</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Lote con MENOR Producción -->
            @if($peorLote)
            <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 shadow-lg rounded-lg p-6 border-2 border-orange-300 dark:border-orange-700">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-400 text-2xl"></i>
                            <h2 class="text-xl font-bold text-orange-800 dark:text-orange-300">Lote con Menor Producción</h2>
                        </div>
                        <p class="text-sm text-orange-600 dark:text-orange-400">Requiere atención</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $peorLote->Nombre }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $peorLote->finca->Nombre ?? 'Sin finca' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">
                                    {{ number_format($peorLote->total_producido) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">huevos totales</div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                            <div class="text-center">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Promedio Diario</div>
                                <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($peorLote->promedio_diario, 1) }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Días con Registro</div>
                                <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ $peorLote->dias_registrados }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 text-sm text-orange-700 dark:text-orange-400">
                        <i class="fas fa-info-circle"></i>
                        <span>Considera revisar condiciones y alimentación de este lote</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @elseif($produccionPorLote->count() === 1)
        <!-- Mensaje cuando solo hay 1 lote con producción -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-6 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-blue-800 dark:text-blue-300">
                        Solo hay un lote con producción registrada
                    </h3>
                    <p class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                        Se necesitan al menos 2 lotes diferentes con producción en el período seleccionado para mostrar la comparación de mejor y peor lote.
                    </p>
                </div>
            </div>
        </div>
        @elseif($produccionPorLote->count() === 0)
        <!-- Mensaje cuando no hay lotes con producción -->
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-6 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-yellow-800 dark:text-yellow-300">
                        No hay registros de producción
                    </h3>
                    <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-400">
                        No se encontraron registros de producción de huevos en el período seleccionado. Registra producción para ver las estadísticas por lote.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold dark:text-white mb-4">Producción diaria</h2>
            <canvas id="chartDaily"></canvas>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold dark:text-white mb-4">Mejores días</h2>
                <ul class="space-y-2">
                    @forelse($mejoresDias as $d)
                        <li class="flex items-center justify-between dark:text-gray-300">
                            <span>{{ \Carbon\Carbon::parse($d->Fecha)->format('d/m/Y') }}</span>
                            <span class="font-semibold">{{ number_format($d->total) }}</span>
                        </li>
                    @empty
                        <li class="text-gray-500 dark:text-gray-400 text-sm">Sin datos</li>
                    @endforelse
                </ul>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold dark:text-white mb-4">Peores días</h2>
                <ul class="space-y-2">
                    @forelse($peoresDias as $d)
                        <li class="flex items-center justify-between dark:text-gray-300">
                            <span>{{ \Carbon\Carbon::parse($d->Fecha)->format('d/m/Y') }}</span>
                            <span class="font-semibold">{{ number_format($d->total) }}</span>
                        </li>
                    @empty
                        <li class="text-gray-500 dark:text-gray-400 text-sm">Sin datos</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 overflow-x-auto">
            <h2 class="text-lg font-semibold dark:text-white mb-4">Registros</h2>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rotos</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Turno</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">% Rotos</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($producciones as $p)
                        <tr>
                            <td class="px-4 py-2 dark:text-gray-300">{{ \Carbon\Carbon::parse($p->Fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 dark:text-gray-300">{{ $p->lote->Nombre ?? '-' }}</td>
                            <td class="px-4 py-2 dark:text-gray-300">{{ number_format($p->CantidadHuevos) }}</td>
                            <td class="px-4 py-2 dark:text-gray-300">{{ number_format($p->HuevosRotos ?? 0) }}</td>
                            <td class="px-4 py-2 dark:text-gray-300">{{ $p->Turno ?? '-' }}</td>
                            <td class="px-4 py-2 dark:text-gray-300">{{ $p->CantidadHuevos > 0 ? number_format(($p->HuevosRotos ?? 0) * 100 / $p->CantidadHuevos, 2) : '0.00' }}%</td>
                            <td class="px-4 py-2">
                                @if($area !== 'employee')
                                    <form method="POST" action="{{ route($area.'.produccion-huevos.destroy', $p->IDProduccion) }}" onsubmit="return confirm('¿Eliminar registro de producción definitivamente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Eliminar</button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500">Sin acciones</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $producciones->links() }}</div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartDaily').getContext('2d');
    const labels = @json($serieDiaria->pluck('Fecha')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m')));
    const data = @json($serieDiaria->pluck('total'));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Huevos por día',
                data: data,
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush
@endsection
