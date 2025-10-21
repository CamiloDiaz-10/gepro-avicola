@extends('layouts.app-with-sidebar')

@section('content')
@php
    $current = Route::currentRouteName();
    $area = 'admin'; // Default
    if (\Illuminate\Support\Str::startsWith($current, 'owner.')) {
        $area = 'owner';
    } elseif (\Illuminate\Support\Str::startsWith($current, 'employee.')) {
        $area = 'employee';
    }
@endphp
<div class="p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reportes Avanzados</h1>
                    <p class="text-gray-600 dark:text-gray-300">Analiza producción, alimentación, sanidad y finanzas</p>
                </div>
                <div class="grid grid-cols-2 lg:flex lg:flex-wrap gap-2">
                    <a href="{{ route($area.'.reports.export.production', request()->query()) }}" class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">Excel </span>Producción
                    </a>
                    <a href="{{ route($area.'.reports.export.feeding', request()->query()) }}" class="inline-flex items-center justify-center px-3 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors text-sm font-medium">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">Excel </span>Alimentación
                    </a>
                    <a href="{{ route($area.'.reports.export.health', request()->query()) }}" class="inline-flex items-center justify-center px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors text-sm font-medium">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">Excel </span>Salud
                    </a>
                    <a href="{{ route($area.'.reports.export.finance', request()->query()) }}" class="inline-flex items-center justify-center px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm font-medium">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">Excel </span>Finanzas
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Filtros de Análisis</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Selecciona finca, lote y rango de fechas para analizar</p>
            </div>
            <form method="GET" action="{{ route($area.'.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4" id="filterForm">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Finca</label>
                    <select name="finca" id="fincaSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todas las fincas</option>
                        @foreach($fincas as $f)
                            <option value="{{ $f->IDFinca }}" {{ (string)request('finca') === (string)$f->IDFinca ? 'selected' : '' }}>
                                {{ $f->Nombre }} ({{ $f->Ubicacion }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Lote</label>
                    <select name="lote" id="loteSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos los lotes</option>
                        @foreach($lotes as $l)
                            <option value="{{ $l->IDLote }}" {{ (string)request('lote') === (string)$l->IDLote ? 'selected' : '' }}>
                                {{ $l->Nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fecha Desde</label>
                    <input type="date" name="desde" value="{{ request('desde') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Fecha Hasta</label>
                    <input type="date" name="hasta" value="{{ request('hasta') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                        Aplicar
                    </button>
                    @if(request('finca') || request('lote') || request('desde') || request('hasta'))
                        <a href="{{ route($area.'.reports.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Producción -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Producción (diaria)</h2>
                </div>
                <div class="h-64"><canvas id="chartProduction"></canvas></div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Top 10 Lotes por Producción</h3>
                    <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1 max-h-40 overflow-auto pr-1">
                        @foreach($production['by_lot'] as $row)
                            <li class="flex justify-between"><span>{{ $row->lote }}</span><span class="font-semibold">{{ $row->total }}</span></li>
                        @endforeach
                        @if($production['by_lot']->isEmpty())
                            <li class="text-gray-500 dark:text-gray-400">Sin datos</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Alimentación -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Alimentación</h2>
                </div>
                <div class="h-64"><canvas id="chartFeeding"></canvas></div>
            </div>

            <!-- Salud -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Salud (Tratamientos)</h2>
                </div>
                <div class="h-64"><canvas id="chartHealth"></canvas></div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Recientes</h3>
                    <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1 max-h-40 overflow-auto pr-1">
                        @foreach($health['recent'] as $r)
                            <li class="flex justify-between"><span>{{ $r->lote }} - {{ $r->TipoTratamiento }}</span><span class="text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($r->Fecha)->format('d/m/Y') }}</span></li>
                        @endforeach
                        @if($health['recent']->isEmpty())
                            <li class="text-gray-500 dark:text-gray-400">Sin datos</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Finanzas -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Finanzas (Movimientos)</h2>
                    <div class="text-sm text-gray-700 dark:text-gray-300">Ventas: <span class="font-semibold">{{ $finance['totals']['ventas'] }}</span> · Compras: <span class="font-semibold">{{ $finance['totals']['compras'] }}</span></div>
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
    // Filtrado dinámico de lotes por finca
    const allLotes = @json($lotes);
    const fincaSelect = document.getElementById('fincaSelect');
    const loteSelect = document.getElementById('loteSelect');
    const currentLoteId = '{{ request('lote') }}';

    if (fincaSelect && loteSelect) {
        fincaSelect.addEventListener('change', function() {
            const fincaId = this.value;
            
            // Limpiar opciones actuales
            loteSelect.innerHTML = '<option value="">Todos los lotes</option>';
            
            // Si no hay finca seleccionada, mostrar todos los lotes
            if (!fincaId) {
                allLotes.forEach(lote => {
                    const option = document.createElement('option');
                    option.value = lote.IDLote;
                    option.textContent = lote.Nombre;
                    loteSelect.appendChild(option);
                });
            } else {
                // Si hay finca seleccionada, hacer petición AJAX para obtener lotes de esa finca
                fetch(`/api/lotes-por-finca/${fincaId}`)
                    .then(response => response.json())
                    .then(lotes => {
                        lotes.forEach(lote => {
                            const option = document.createElement('option');
                            option.value = lote.IDLote;
                            option.textContent = lote.Nombre;
                            loteSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar lotes:', error);
                    });
            }
        });
    }

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
