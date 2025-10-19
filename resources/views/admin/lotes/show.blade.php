@extends('layouts.app-with-sidebar')

@section('content')
<style>
    /* Estilos del scrollbar (WebKit) y Firefox */
    /* Base (aplica también a Firefox) */
    .custom-scrollbar {
        scrollbar-width: thin; /* Firefox */
        scrollbar-color: #9ca3af #f1f1f1; /* thumb track */
    }

    /* Modo claro (WebKit) */
    .custom-scrollbar::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #9ca3af;
        border-radius: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }

    /* Modo oscuro (WebKit + Firefox) */
    .dark .custom-scrollbar {
        scrollbar-width: thin; /* Firefox */
        scrollbar-color: #e5e7eb #1f2937; /* thumb claro sobre track oscuro */
    }
    .dark .custom-scrollbar::-webkit-scrollbar-track {
        background: #1f2937; /* gris muy oscuro */
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e5e7eb; /* claro SIEMPRE visible */
        border: 2px solid #1f2937; /* mayor contraste con el track */
        border-radius: 6px;
    }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #f3f4f6; /* aún más claro al pasar el cursor */
    }
</style>

<div class="p-6">
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Lote: {{ $lote->Nombre }}</h1>
                <p class="text-gray-600 dark:text-gray-300">Detalles del lote</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.lotes.edit', $lote) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Editar</a>
                <a href="{{ route('admin.lotes.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-200 dark:hover:bg-gray-500">Volver</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información General</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Nombre</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $lote->Nombre }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Finca</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $lote->finca->Nombre ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Ubicación</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $lote->finca->Ubicacion ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Fecha de Ingreso</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ optional($lote->FechaIngreso)->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Cantidad Inicial</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($lote->CantidadInicial) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Raza</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $lote->Raza ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Estadísticas Básicas</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded bg-gray-50 dark:bg-gray-700">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Edad (días)</div>
                            <div class="text-xl font-semibold dark:text-white">{{ $lote->edad_en_dias }}</div>
                        </div>
                        <div class="p-4 rounded bg-gray-50 dark:bg-gray-700">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Producción (últimos 7 días)</div>
                            <div class="text-xl font-semibold dark:text-white">—</div>
                        </div>
                        <div class="p-4 rounded bg-gray-50 dark:bg-gray-700">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Mortalidad (30 días)</div>
                            <div class="text-xl font-semibold dark:text-white">—</div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas Básicas de Aves -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resumen de Aves en el Lote</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Total de Aves -->
                        <div class="p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-blue-600 dark:text-blue-300">Total de Aves</p>
                                    <p class="mt-1 text-2xl font-bold text-blue-700 dark:text-blue-200">{{ number_format($totalGallinas) }}</p>
                                </div>
                                <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-800/50">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Aves Activas -->
                        <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-600 dark:text-green-300">Aves Activas</p>
                                    <p class="mt-1 text-2xl font-bold text-green-700 dark:text-green-200">{{ number_format($gallinasActivas) }}</p>
                                    @if($totalGallinas > 0)
                                    <p class="mt-1 text-xs text-green-500 dark:text-green-400">
                                        {{ round(($gallinasActivas / $totalGallinas) * 100, 1) }}% del total
                                    </p>
                                    @endif
                                </div>
                                <div class="p-2 rounded-full bg-green-100 dark:bg-green-800/50">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Aves Inactivas -->
                        <div class="p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-amber-600 dark:text-amber-300">Aves Inactivas</p>
                                    <p class="mt-1 text-2xl font-bold text-amber-700 dark:text-amber-200">{{ number_format($gallinasInactivas) }}</p>
                                    @if($totalGallinas > 0)
                                    <p class="mt-1 text-xs text-amber-500 dark:text-amber-400">
                                        {{ round(($gallinasInactivas / $totalGallinas) * 100, 1) }}% del total
                                    </p>
                                    @endif
                                </div>
                                <div class="p-2 rounded-full bg-amber-100 dark:bg-amber-800/50">
                                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Aves Vendidas -->
                        <div class="p-4 rounded-lg bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-purple-600 dark:text-purple-300">Aves Vendidas</p>
                                    <p class="mt-1 text-2xl font-bold text-purple-700 dark:text-purple-200">{{ number_format($gallinasVendidas) }}</p>
                                    @if($totalGallinas > 0)
                                    <p class="mt-1 text-xs text-purple-500 dark:text-purple-400">
                                        {{ round(($gallinasVendidas / $totalGallinas) * 100, 1) }}% del total
                                    </p>
                                    @endif
                                </div>
                                <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-800/50">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Aves -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Aves del Lote</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Listado de todas las aves registradas en este lote</p>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $lote->gallinas->count() }} {{ $lote->gallinas->count() === 1 ? 'ave' : 'aves' }}
                            </div>
                        </div>
                    </div>
                    
                    @if($lote->gallinas->count() > 0)
                        <div class="overflow-x-auto max-h-96 overflow-y-auto relative custom-scrollbar">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700">Fecha Nacimiento</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700">Edad</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700">Peso (kg)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700">Estado</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-700">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($lote->gallinas as $gallina)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">#{{ $gallina->IDGallina }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">{{ $gallina->tipoGallina->Nombre ?? '—' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-300">
                                                    {{ $gallina->FechaNacimiento ? $gallina->FechaNacimiento->format('d/m/Y') : '—' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-300">
                                                    {{ $gallina->edad_en_dias }} días
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $gallina->edad_en_semanas }} sem)</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-300">
                                                    {{ $gallina->Peso ? number_format($gallina->Peso, 2) : '—' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $estado = strtolower(trim($gallina->Estado));
                                                    $esVendida = str_contains($estado, 'vendida') || str_contains($estado, 'vendido');
                                                    $esActiva = $estado === 'activa' || $estado === 'activo';
                                                    $esInactiva = !$esVendida && !$esActiva;
                                                @endphp
                                                
                                                @if($esActiva)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                                        Activa
                                                    </span>
                                                @elseif($esVendida)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                                        Vendida
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                        Inactiva
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <form action="{{ route('admin.aves.destroy', $gallina->IDGallina) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar esta ave del lote? Esta acción no se puede deshacer.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm transition-colors" title="Eliminar ave">
                                                        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No hay aves registradas</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Este lote aún no tiene aves asignadas.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acciones</h2>
                    <form action="{{ route('admin.lotes.destroy', $lote) }}" method="POST" onsubmit="return confirm('¿Eliminar este lote?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
