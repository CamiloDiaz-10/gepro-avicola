@extends('layouts.app-with-sidebar')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Fincas</h1>
            <p class="text-gray-600">Administra las fincas registradas en el sistema</p>
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

        <div class="flex items-center justify-between">
            <form method="GET" action="{{ route('admin.fincas.index') }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o ubicación" class="w-64 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Buscar</button>
            </form>
            <a href="{{ route('admin.fincas.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Nueva Finca</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($fincas as $finca)
                <div class="bg-white rounded-lg shadow p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $finca->Nombre }}</h3>
                            <p class="text-gray-600">{{ $finca->Ubicacion }}</p>
                        </div>
                        <span class="text-xs text-gray-500">ID: {{ $finca->IDFinca }}</span>
                    </div>

                    <div class="mt-3 text-sm text-gray-700 space-y-1">
                        @if(!is_null($finca->Hectareas))
                            <p><span class="font-medium">Hectáreas:</span> {{ number_format($finca->Hectareas, 2) }}</p>
                        @endif
                        @if(!is_null($finca->Latitud) && !is_null($finca->Longitud))
                            <p><span class="font-medium">Coordenadas:</span> {{ $finca->Latitud }}, {{ $finca->Longitud }}</p>
                        @endif
                    </div>

                    <div class="mt-4 flex items-center gap-2">
                        <a href="{{ route('admin.fincas.show', $finca) }}" class="px-3 py-1.5 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded">Ver</a>
                        <a href="{{ route('admin.fincas.edit', $finca) }}" class="px-3 py-1.5 text-yellow-700 bg-yellow-50 hover:bg-yellow-100 rounded">Editar</a>
                        <form action="{{ route('admin.fincas.destroy', $finca) }}" method="POST" onsubmit="return confirm('¿Eliminar esta finca?');" class="ml-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 text-red-700 bg-red-50 hover:bg-red-100 rounded">Eliminar</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-3">
                    <div class="bg-white rounded-lg shadow p-8 text-center text-gray-600">No hay fincas registradas.</div>
                </div>
            @endforelse
        </div>

        <div>
            {{ $fincas->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
