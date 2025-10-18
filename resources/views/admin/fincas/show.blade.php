@extends('layouts.app-with-sidebar')

@section('content')
<div class="p-6">
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $finca->Nombre }}</h1>
                <p class="text-gray-600 dark:text-gray-300">Detalles de la finca</p>
            </div>
            <div class="flex items-center gap-2">
                @if(!request()->routeIs('employee.*'))
                    <a href="{{ route('admin.fincas.edit', $finca) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Editar</a>
                @endif
                <a href="{{ route(request()->routeIs('employee.*') ? 'employee.fincas.index' : 'admin.fincas.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-200 dark:hover:bg-gray-500">Volver</a>
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
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $finca->Nombre }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Ubicación</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $finca->Ubicacion }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Hectáreas</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ !is_null($finca->Hectareas) ? number_format($finca->Hectareas, 2) : '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Coordenadas</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                @if(!is_null($finca->Latitud) && !is_null($finca->Longitud))
                                    {{ $finca->Latitud }}, {{ $finca->Longitud }}
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Creada</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $finca->created_at?->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Actualizada</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $finca->updated_at?->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Usuarios Asignados</h2>
                    @if($finca->users->count())
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($finca->users as $user)
                                <li class="py-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->Nombre }} {{ $user->Apellido }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->Email }}</p>
                                    </div>
                                    <div class="text-xs text-gray-600 dark:text-gray-300">
                                        {{ $user->pivot->RolEnFinca ?? '—' }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay usuarios asignados a esta finca.</p>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                @if(!request()->routeIs('employee.*'))
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acciones</h2>
                        <form action="{{ route('admin.fincas.destroy', $finca) }}" method="POST" onsubmit="return confirm('¿Eliminar esta finca?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
