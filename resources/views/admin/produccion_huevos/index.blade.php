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
