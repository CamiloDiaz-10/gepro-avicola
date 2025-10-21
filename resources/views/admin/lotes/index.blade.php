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
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Lotes</h1>
            <p class="text-gray-600 dark:text-gray-300">Administra los lotes de aves por finca</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Filtros de Búsqueda</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Busca por nombre, raza o finca</p>
                </div>
                @if($area !== 'employee')
                <a href="{{ route($area.'.lotes.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Lote
                </a>
                @endif
            </div>

            <form method="GET" action="{{ route($area.'.lotes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o raza del lote" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Finca</label>
                    <select name="finca" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todas las fincas</option>
                        @foreach($fincas as $finca)
                            <option value="{{ $finca->IDFinca }}" {{ request('finca') == $finca->IDFinca ? 'selected' : '' }}>
                                {{ $finca->Nombre }} ({{ $finca->Ubicacion }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                        Filtrar
                    </button>
                    @if(request('search') || request('finca'))
                        <a href="{{ route($area.'.lotes.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Lote</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Finca</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Ingreso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cant. Inicial</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($lotes as $lote)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $lote->Nombre }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Raza: {{ $lote->Raza ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $lote->finca->Nombre ?? '—' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $lote->finca->Ubicacion ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ optional($lote->FechaIngreso)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300">{{ number_format($lote->CantidadInicial) }}</td>
                            <td class="px-6 py-4 text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route($area.'.lotes.show', $lote) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">Ver</a>
                                    @if($area !== 'employee')
                                    <a href="{{ route($area.'.lotes.edit', $lote) }}" class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400">Editar</a>
                                    <form action="{{ route($area.'.lotes.destroy', $lote) }}" method="POST" onsubmit="return confirm('¿Eliminar este lote?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800 dark:text-red-400">Eliminar</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No hay lotes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $lotes->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
