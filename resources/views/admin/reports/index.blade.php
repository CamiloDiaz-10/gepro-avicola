@extends('layouts.app-with-sidebar')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reportes Avanzados</h1>
                <p class="text-gray-600">Analiza producción, alimentación, sanidad y finanzas</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.export.production', request()->query()) }}" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Exportar Producción</a>
                <a href="{{ route('admin.reports.export.feeding', request()->query()) }}" class="px-3 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">Exportar Alimentación</a>
                <a href="{{ route('admin.reports.export.health', request()->query()) }}" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">Exportar Salud</a>
                <a href="{{ route('admin.reports.export.finance', request()->query()) }}" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">Exportar Finanzas</a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Finca</label>
                    <select name="finca" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas</option>
                        @foreach($fincas as $f)
                            <option value="{{ $f->IDFinca }}" {{ (string)request('finca') === (string)$f->IDFinca ? 'selected' : '' }}>
                                {{ $f->Nombre }} ({{ $f->Ubicacion }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Desde</label>
                    <input type="date" name="desde" value="{{ request('desde') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Hasta</label>
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Aplicar</button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Producción -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Producción (diaria)</h2>
                </div>
                <div class="h-64"><canvas id="chartProduction"></canvas></div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Top 10 Lotes por Producción</h3>
                    <ul class="text-sm text-gray-700 space-y-1 max-h-40 overflow-auto pr-1">
                        @foreach($production['by_lot'] as $row)
                            <li class="flex justify-between"><span>{{ $row->lote }}</span><span class="font-semibold">{{ $row->total }}</span></li>
                        @endforeach
                        @if($production['by_lot']->isEmpty())
                            <li class="text-gray-500">Sin datos</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Alimentación -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Alimentación</h2>
                </div>
                <div class="h-64"><canvas id="chartFeeding"></canvas></div>
            </div>

            <!-- Salud -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Salud (Tratamientos)</h2>
                </div>
                <div class="h-64"><canvas id="chartHealth"></canvas></div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Recientes</h3>
                    <ul class="text-sm text-gray-700 space-y-1 max-h-40 overflow-auto pr-1">
                        @foreach($health['recent'] as $r)
                            <li class="flex justify-between"><span>{{ $r->lote }} - {{ $r->TipoTratamiento }}</span><span class="text-gray-500">{{ \Carbon\Carbon::parse($r->Fecha)->format('d/m/Y') }}</span></li>
                        @endforeach
                        @if($health['recent']->isEmpty())
                            <li class="text-gray-500">Sin datos</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Finanzas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Finanzas (Movimientos)</h2>
                    <div class="text-sm text-gray-700">Ventas: <span class="font-semibold">{{ $finance['totals']['ventas'] }}</span> · Compras: <span class="font-semibold">{{ $finance['totals']['compras'] }}</span></div>
                </div>
                <div class="h-64"><canvas id="chartFinance"></canvas></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const prodDaily = @json($production['daily']);
    const feedDaily = @json($feeding['daily']);
    const feedType = @json($feeding['by_type']);
    const healthTreat = @json($health['treatments']);
    const financeMov = @json($finance['movements']);

    // Producción (línea)
    new Chart(document.getElementById('chartProduction'), {
        type: 'line',
        data: {
            labels: prodDaily.map(x => x.date),
            datasets: [{
                label: 'Huevos',
                data: prodDaily.map(x => x.total),
                borderColor: 'rgb(59,130,246)',
                backgroundColor: 'rgba(59,130,246,0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Alimentación (barras por tipo)
    new Chart(document.getElementById('chartFeeding'), {
        type: 'bar',
        data: {
            labels: feedType.map(x => x.feed_type),
            datasets: [{
                label: 'Kg (30d)',
                data: feedType.map(x => x.total),
                backgroundColor: 'rgba(234,179,8,0.6)',
                borderColor: 'rgb(202,138,4)'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Salud (pie por tratamiento)
    new Chart(document.getElementById('chartHealth'), {
        type: 'pie',
        data: {
            labels: healthTreat.map(x => x.treatment),
            datasets: [{
                data: healthTreat.map(x => x.total),
                backgroundColor: ['#60a5fa','#fb7185','#34d399','#fbbf24','#a78bfa','#f87171']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Finanzas (línea por día, total de movimientos)
    const groupedFinance = financeMov.reduce((acc, cur) => {
        acc[cur.Fecha] = (acc[cur.Fecha] || 0) + Number(cur.total);
        return acc;
    }, {});
    const financeLabels = Object.keys(groupedFinance).sort();
    new Chart(document.getElementById('chartFinance'), {
        type: 'line',
        data: {
            labels: financeLabels,
            datasets: [{
                label: 'Movimientos',
                data: financeLabels.map(k => groupedFinance[k]),
                borderColor: 'rgb(34,197,94)',
                backgroundColor: 'rgba(34,197,94,0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endpush
