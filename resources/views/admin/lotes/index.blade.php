@extends('layouts.app-with-sidebar')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Lotes</h1>
            <p class="text-gray-600">Administra los lotes de aves por finca</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex items-center justify-between gap-3 flex-wrap">
            <form method="GET" action="{{ route('admin.lotes.index') }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o raza" class="w-64 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <select name="finca" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas las fincas</option>
                    @foreach($fincas as $finca)
                        <option value="{{ $finca->IDFinca }}" {{ request('finca') == $finca->IDFinca ? 'selected' : '' }}>
                            {{ $finca->Nombre }} ({{ $finca->Ubicacion }})
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Filtrar</button>
            </form>
            <a href="{{ route('admin.lotes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Nuevo Lote</a>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Finca</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Ingreso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cant. Inicial</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lotes as $lote)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $lote->Nombre }}</div>
                                <div class="text-xs text-gray-500">Raza: {{ $lote->Raza ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $lote->finca->Nombre ?? '—' }}</div>
                                <div class="text-xs text-gray-500">{{ $lote->finca->Ubicacion ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ optional($lote->FechaIngreso)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($lote->CantidadInicial) }}</td>
                            <td class="px-6 py-4 text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.lotes.show', $lote) }}" class="text-blue-600 hover:text-blue-800">Ver</a>
                                    <a href="{{ route('admin.lotes.edit', $lote) }}" class="text-yellow-600 hover:text-yellow-800">Editar</a>
                                    <form action="{{ route('admin.lotes.destroy', $lote) }}" method="POST" onsubmit="return confirm('¿Eliminar este lote?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-800">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No hay lotes registrados.</td>
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
