@extends('layouts.app-with-sidebar')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Fincas</h1>
                    <p class="text-gray-600 dark:text-gray-300">Administra las fincas registradas en el sistema</p>
                </div>
                @if(!request()->routeIs('employee.*'))
                <a href="{{ route('admin.fincas.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors font-medium shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nueva Finca
                </a>
                @endif
            </div>

            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form method="GET" action="{{ route(request()->routeIs('employee.*') ? 'employee.fincas.index' : 'admin.fincas.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Buscar</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre o ubicación de la finca" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Buscar
                    </button>
                    @if(request('search'))
                        <a href="{{ route(request()->routeIs('employee.*') ? 'employee.fincas.index' : 'admin.fincas.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-200 dark:hover:bg-gray-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @forelse($fincas as $finca)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $finca->Nombre }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $finca->Ubicacion }}
                            </p>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">ID: {{ $finca->IDFinca }}</span>
                    </div>

                    <div class="mt-3 text-sm text-gray-700 dark:text-gray-300 space-y-2 border-t border-gray-200 dark:border-gray-700 pt-3">
                        @if(!is_null($finca->Hectareas))
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                </svg>
                                <span class="font-medium">Hectáreas:</span>
                                <span class="ml-1">{{ number_format($finca->Hectareas, 2) }}</span>
                            </div>
                        @endif
                        @if(!is_null($finca->Latitud) && !is_null($finca->Longitud))
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                                <span class="font-medium">Coordenadas:</span>
                                <span class="ml-1 text-xs">{{ $finca->Latitud }}, {{ $finca->Longitud }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-wrap items-center gap-2">
                        <a href="{{ route(request()->routeIs('employee.*') ? 'employee.fincas.show' : 'admin.fincas.show', $finca) }}" class="flex-1 sm:flex-none px-3 py-2 text-center text-sm font-medium text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-md transition-colors">
                            Ver
                        </a>
                        @if(!request()->routeIs('employee.*'))
                            <a href="{{ route('admin.fincas.edit', $finca) }}" class="flex-1 sm:flex-none px-3 py-2 text-center text-sm font-medium text-yellow-700 dark:text-yellow-300 bg-yellow-50 dark:bg-yellow-900/30 hover:bg-yellow-100 dark:hover:bg-yellow-900/50 rounded-md transition-colors">
                                Editar
                            </a>
                            <form action="{{ route('admin.fincas.destroy', $finca) }}" method="POST" onsubmit="return confirm('¿Eliminar esta finca?');" class="flex-1 sm:flex-none sm:ml-auto">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-3 py-2 text-sm font-medium text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-md transition-colors">
                                    Eliminar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        @if(request()->routeIs('employee.*'))
                            <p class="text-gray-600 dark:text-gray-400">No tienes fincas asignadas.</p>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">No hay fincas registradas.</p>
                            <a href="{{ route('admin.fincas.create') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Crear Primera Finca
                            </a>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <div>
            {{ $fincas->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
