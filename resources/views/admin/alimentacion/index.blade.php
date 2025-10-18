@extends('layouts.app-with-sidebar')

@section('title', 'Gestión de Alimentación')

@section('content')
@php
    $current = Route::currentRouteName();
    $area = \Illuminate\Support\Str::startsWith($current, 'veterinario.') ? 'veterinario' : 'admin';
@endphp
<div class="p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Gestión de Alimentación</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Filtra por fechas, lote y tipo de alimento</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route($area.'.alimentacion.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Registrar Alimentación</a>
                    <a href="{{ route($area.'.alimentacion.export.csv', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500">Exportar CSV</a>
                </div>
            </div>

            <form method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-6 gap-4">
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
                        @foreach($lotes as $l)
                            <option value="{{ $l->IDLote }}" {{ ($filters['loteId'] ?? '') == $l->IDLote ? 'selected' : '' }}>{{ $l->Nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Tipo</label>
                    <select name="tipo" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Todos</option>
                        @foreach($tipos as $t)
                            <option value="{{ $t->IDTipoAlimento }}" {{ ($filters['tipoId'] ?? '') == $t->IDTipoAlimento ? 'selected' : '' }}>{{ $t->Nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end">
                    <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500">Aplicar</button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-gray-500 dark:text-gray-400 text-sm">Total alimento</div>
                <div class="text-3xl font-bold dark:text-white">{{ number_format($totales->total_cantidad ?? 0, 2) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 lg:col-span-2">
                <div class="text-gray-500 dark:text-gray-400 text-sm mb-2">Consumo diario</div>
                <div class="h-40">
                    <canvas id="chartFeedDaily" height="160"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold dark:text-white mb-4">Por Tipo de Alimento</h2>
                <div class="h-40">
                    <canvas id="chartFeedByType" height="160"></canvas>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold dark:text-white mb-4">Por Lote</h2>
                <div class="h-40">
                    <canvas id="chartFeedByLote" height="160"></canvas>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 overflow-x-auto">
            <h2 class="text-lg font-semibold dark:text-white mb-4">Registros</h2>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad (kg)</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Costo Estimado</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($registros as $r)
                        <tr>
                            <td class="px-4 py-2 dark:text-gray-300">{{ \Carbon\Carbon::parse($r->Fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 dark:text-gray-300">{{ $r->lote->Nombre ?? '-' }}</td>
                            <td class="px-4 py-2 dark:text-gray-300">{{ $r->tipoAlimento->Nombre ?? '-' }}</td>
                            <td class="px-4 py-2 dark:text-gray-300">{{ number_format($r->CantidadKg, 2) }}</td>
                            <td class="px-4 py-2 dark:text-gray-300">${{ number_format((optional($r->tipoAlimento)->PrecioPorKg ?? 0) * ($r->CantidadKg ?? 0), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $registros->links() }}</div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos
    const dailyLabels = @json($charts['daily']->pluck('Fecha')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m')));
    const dailyData = @json($charts['daily']->pluck('total'));

    const typeLabels = @json($charts['by_type']->map(fn($row) => optional($row->tipoAlimento)->Nombre ?? 'Otro'));
    const typeData = @json($charts['by_type']->pluck('total'));

    const loteLabels = @json($charts['by_lote']->map(fn($row) => optional($row->lote)->Nombre ?? 'Lote'));
    const loteData = @json($charts['by_lote']->pluck('total'));

    // Gráfico pequeño: Consumo diario
    new Chart(document.getElementById('chartFeedDaily').getContext('2d'), {
        type: 'line',
        data: { labels: dailyLabels, datasets: [{ data: dailyData, label: 'Kg/día', borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.2)', fill: true, tension: .3 }] },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
    });


    // Gráfico pequeño: Por Tipo
    new Chart(document.getElementById('chartFeedByType').getContext('2d'), {
        type: 'doughnut',
        data: { labels: typeLabels, datasets: [{ data: typeData, backgroundColor: ['#60a5fa','#a78bfa','#34d399','#fbbf24','#f87171','#10b981'] }] },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Gráfico pequeño: Por Lote
    new Chart(document.getElementById('chartFeedByLote').getContext('2d'), {
        type: 'bar',
        data: { labels: loteLabels, datasets: [{ data: loteData, backgroundColor: '#34d399' }] },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
    });
</script>
@endpush
@endsection
