@extends('layouts.app-with-sidebar')

@section('title', 'Gestión de Aves')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Gestión de Aves</h1>
                    <p class="text-gray-500 text-sm">Filtra por lote, tipo, estado y fecha de nacimiento</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.aves.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Registrar Ave
                    </a>
                    <a href="{{ route('admin.aves.export.csv', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Exportar CSV
                    </a>
                </div>
            </div>

            <form method="GET" class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lote</label>
                    <select name="lote" class="w-full rounded-md border-gray-300">
                        <option value="">Todos</option>
                        @foreach($lotes as $l)
                            <option value="{{ $l->IDLote }}" {{ $filters['lote'] == $l->IDLote ? 'selected' : '' }}>{{ $l->Nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <select name="tipo" class="w-full rounded-md border-gray-300">
                        <option value="">Todos</option>
                        @foreach($tipos as $t)
                            <option value="{{ $t->IDTipoGallina }}" {{ $filters['tipo'] == $t->IDTipoGallina ? 'selected' : '' }}>{{ $t->Nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" class="w-full rounded-md border-gray-300">
                        <option value="">Todos</option>
                        @foreach(['Viva','Muerta','Vendida','Trasladada'] as $estado)
                            <option value="{{ $estado }}" {{ $filters['estado'] == $estado ? 'selected' : '' }}>{{ $estado }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nacidas desde</label>
                    <input type="date" name="born_from" value="{{ $filters['born_from'] }}" class="w-full rounded-md border-gray-300"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nacidas hasta</label>
                    <input type="date" name="born_to" value="{{ $filters['born_to'] }}" class="w-full rounded-md border-gray-300"/>
                </div>
                <div class="md:col-span-5 flex items-end">
                    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">Aplicar</button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="text-gray-500 text-sm">Total de Aves</div>
                <div class="text-3xl font-bold">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="bg-white shadow rounded-lg p-6 lg:col-span-2">
                <div class="text-gray-500 text-sm mb-2">Por Estado</div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @forelse($stats['by_status'] as $row)
                        <div class="p-3 bg-gray-50 rounded text-center">
                            <div class="text-xs text-gray-500">{{ $row->status }}</div>
                            <div class="text-lg font-semibold">{{ $row->total }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Sin datos</div>
                    @endforelse
                </div>
            </div>
            <div class="bg-white shadow rounded-lg p-6">
                <div class="text-gray-500 text-sm">Adquisiciones (7 días)</div>
                <div class="text-3xl font-bold">{{ number_format($stats['recent']) }}</div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Listado</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nacimiento</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Peso (g)</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($birds as $b)
                            <tr>
                                <td class="px-4 py-2">{{ $b->IDGallina }}</td>
                                <td class="px-4 py-2">{{ $b->lote->Nombre ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $b->tipoGallina->Nombre ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $b->FechaNacimiento ? \Carbon\Carbon::parse($b->FechaNacimiento)->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-2">{{ $b->Peso }}</td>
                                <td class="px-4 py-2">{{ $b->Estado }}</td>
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
    container.className = 'bg-white shadow rounded-lg p-6';
    const title = document.createElement('h2');
    title.className = 'text-lg font-semibold mb-4';
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
    const c1 = document.createElement('div'); c1.className = 'bg-white shadow rounded-lg p-6';
    const c2 = document.createElement('div'); c2.className = 'bg-white shadow rounded-lg p-6';
    const t1 = document.createElement('h2'); t1.className = 'text-lg font-semibold mb-4'; t1.textContent = 'Distribución por Estado';
    const t2 = document.createElement('h2'); t2.className = 'text-lg font-semibold mb-4'; t2.textContent = 'Distribución por Tipo';
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
