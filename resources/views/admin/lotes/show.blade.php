@extends('layouts.app-with-sidebar')

@section('content')
<div class="p-6">
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Lote: {{ $lote->Nombre }}</h1>
                <p class="text-gray-600">Detalles del lote</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.lotes.edit', $lote) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Editar</a>
                <a href="{{ route('admin.lotes.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Volver</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Información General</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500">Nombre</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $lote->Nombre }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Finca</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $lote->finca->Nombre ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Ubicación</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $lote->finca->Ubicacion ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Fecha de Ingreso</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ optional($lote->FechaIngreso)->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Cantidad Inicial</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ number_format($lote->CantidadInicial) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Raza</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $lote->Raza ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas Básicas</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded bg-gray-50">
                            <div class="text-xs text-gray-500">Edad (días)</div>
                            <div class="text-xl font-semibold">{{ $lote->edad_en_dias }}</div>
                        </div>
                        <div class="p-4 rounded bg-gray-50">
                            <div class="text-xs text-gray-500">Producción (últimos 7 días)</div>
                            <div class="text-xl font-semibold">—</div>
                        </div>
                        <div class="p-4 rounded bg-gray-50">
                            <div class="text-xs text-gray-500">Mortalidad (30 días)</div>
                            <div class="text-xl font-semibold">—</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Acciones</h2>
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
