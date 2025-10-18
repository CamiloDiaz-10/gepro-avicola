@extends('layouts.app-with-sidebar')

@section('title', 'Gestión de Aves')

@section('content')
@php
    $current = Route::currentRouteName();
    $area = \Illuminate\Support\Str::startsWith($current, 'owner.') ? 'owner' : (\Illuminate\Support\Str::startsWith($current, 'veterinario.') ? 'veterinario' : 'admin');
    $isVeterinario = auth()->check() && auth()->user()->role && auth()->user()->role->NombreRol === 'Veterinario';
@endphp
<div class="p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Gestión de Aves</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Filtra por lote, tipo, estado y fecha de nacimiento</p>
                </div>
                <div class="flex gap-2">
                    @if(!$isVeterinario)
                    <a href="{{ route($area.'.aves.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Registrar Ave
                    </a>
                    @endif
                    <a href="{{ route($area.'.aves.export.csv', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500">
                        Exportar CSV
                    </a>
                    @if(!$isVeterinario)
                    <a href="{{ route($area.'.aves.scan') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Escanear Código QR
                    </a>
                    @endif
                </div>
            </div>

            <form method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Lote</label>
                    <select name="lote" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Todos</option>
                        @foreach($lotes as $l)
                            <option value="{{ $l->IDLote }}" {{ $filters['lote'] == $l->IDLote ? 'selected' : '' }}>{{ $l->Nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Tipo</label>
                    <select name="tipo" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Todos</option>
                        @foreach($tipos as $t)
                            <option value="{{ $t->IDTipoGallina }}" {{ $filters['tipo'] == $t->IDTipoGallina ? 'selected' : '' }}>{{ $t->Nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Estado</label>
                    <select name="estado" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">Todos</option>
                        @foreach(['Activa','Muerta','Vendida'] as $estado)
                            <option value="{{ $estado }}" {{ $filters['estado'] == $estado ? 'selected' : '' }}>{{ $estado }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Nacidas desde</label>
                    <input type="date" name="born_from" value="{{ $filters['born_from'] }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Nacidas hasta</label>
                    <input type="date" name="born_to" value="{{ $filters['born_to'] }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"/>
                </div>
                <div class="md:col-span-5 flex items-end">
                    <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500">Aplicar</button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-gray-500 dark:text-gray-400 text-sm">Total de Aves</div>
                <div class="text-3xl font-bold dark:text-white">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 lg:col-span-2">
                <div class="text-gray-500 dark:text-gray-400 text-sm mb-2">Por Estado</div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @forelse($stats['by_status'] as $row)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded text-center">
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $row->status }}</div>
                            <div class="text-lg font-semibold dark:text-white">{{ $row->total }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 dark:text-gray-400">Sin datos</div>
                    @endforelse
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="text-gray-500 dark:text-gray-400 text-sm">Adquisiciones (7 días)</div>
                <div class="text-3xl font-bold dark:text-white">{{ number_format($stats['recent']) }}</div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold dark:text-white mb-4">Listado</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Lote</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tipo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nacimiento</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Peso (g)</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Estado</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($birds as $b)
                            <tr>
                                <td class="px-4 py-2 dark:text-gray-300">{{ $b->IDGallina }}</td>
                                <td class="px-4 py-2 dark:text-gray-300">{{ $b->lote->Nombre ?? '-' }}</td>
                                <td class="px-4 py-2 dark:text-gray-300">{{ $b->tipoGallina->Nombre ?? '-' }}</td>
                                <td class="px-4 py-2 dark:text-gray-300">{{ $b->FechaNacimiento ? \Carbon\Carbon::parse($b->FechaNacimiento)->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-2 dark:text-gray-300">{{ $b->Peso }}</td>
                                <td class="px-4 py-2 dark:text-gray-300">{{ $b->Estado }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-3">
                                        @if(!empty($b->qr_token))
                                            <a href="{{ route($area.'.aves.show.byqr', $b->qr_token) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">Ver Detalles</a>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500">Sin QR</span>
                                        @endif

                                        @if(!$isVeterinario)
                                        <form method="POST" action="{{ route($area.'.aves.destroy', $b->IDGallina) }}" onsubmit="return confirm('¿Eliminar ave definitivamente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">Eliminar</button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $birds->links() }}</div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line chart: births per day
    const birthsCtx = document.createElement('canvas');
    birthsCtx.id = 'chartBirdBirths';
    const firstCard = document.querySelector('.max-w-7xl .space-y-6');
    // Insert chart block above the list
    const container = document.createElement('div');
    container.className = 'bg-white dark:bg-gray-800 shadow rounded-lg p-6';
    const title = document.createElement('h2');
    title.className = 'text-lg font-semibold dark:text-white mb-4';
    title.textContent = 'Nacimientos por día';
    container.appendChild(title);
    container.appendChild(birthsCtx);
    document.querySelector('.max-w-7xl.mx-auto').insertBefore(container, document.querySelector('.max-w-7xl.mx-auto').children[2]);

    const birthsLabels = @json($charts['births_series']->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m')));
    const birthsData = @json($charts['births_series']->pluck('total'));

    new Chart(birthsCtx.getContext('2d'), {
        type: 'line',
        data: {
            labels: birthsLabels,
            datasets: [{
                label: 'Aves nacidas',
                data: birthsData,
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // Doughnut charts: by status and by type
    const statusCanvas = document.createElement('canvas');
    const typeCanvas = document.createElement('canvas');
    const grid = document.createElement('div');
    grid.className = 'grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6';
    const c1 = document.createElement('div'); c1.className = 'bg-white dark:bg-gray-800 shadow rounded-lg p-6';
    const c2 = document.createElement('div'); c2.className = 'bg-white dark:bg-gray-800 shadow rounded-lg p-6';
    const t1 = document.createElement('h2'); t1.className = 'text-lg font-semibold dark:text-white mb-4'; t1.textContent = 'Distribución por Estado';
    const t2 = document.createElement('h2'); t2.className = 'text-lg font-semibold dark:text-white mb-4'; t2.textContent = 'Distribución por Tipo';
    c1.appendChild(t1); c1.appendChild(statusCanvas);
    c2.appendChild(t2); c2.appendChild(typeCanvas);
    grid.appendChild(c1); grid.appendChild(c2);
    document.querySelector('.max-w-7xl.mx-auto').insertBefore(grid, document.querySelector('.max-w-7xl.mx-auto').children[3]);

    const statusLabels = @json($charts['status']->pluck('status'));
    const statusData = @json($charts['status']->pluck('total'));
    const typeLabels = @json($charts['types']->map(fn($r) => optional($r->tipoGallina)->Nombre ?? 'Sin tipo'));
    const typeData = @json($charts['types']->pluck('total'));

    new Chart(statusCanvas.getContext('2d'), {
        type: 'doughnut',
        data: { labels: statusLabels, datasets: [{ data: statusData, backgroundColor: ['#60a5fa','#f87171','#34d399','#fbbf24'] }] },
        options: { responsive: true }
    });
    new Chart(typeCanvas.getContext('2d'), {
        type: 'doughnut',
        data: { labels: typeLabels, datasets: [{ data: typeData, backgroundColor: ['#a78bfa','#f97316','#10b981','#3b82f6','#ef4444','#22c55e'] }] },
        options: { responsive: true }
    });
</script>
@endpush
@endsection
