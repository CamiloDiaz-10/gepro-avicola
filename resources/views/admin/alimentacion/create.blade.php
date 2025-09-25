@extends('layouts.app-with-sidebar')

@section('title', 'Registrar Alimentación')

@section('content')
<div class="p-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-800">Registrar Alimentación</h1>
                <a href="{{ route('admin.alimentacion.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Ver Registros</a>
            </div>

            @if(session('error'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-700">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.alimentacion.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                    <input type="date" name="Fecha" value="{{ old('Fecha', $hoy) }}" max="{{ date('Y-m-d') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('Fecha')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lote</label>
                    <select name="IDLote" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Seleccione lote</option>
                        @foreach($lotes as $lote)
                            <option value="{{ $lote->IDLote }}" {{ old('IDLote') == $lote->IDLote ? 'selected' : '' }}>{{ $lote->Nombre }}</option>
                        @endforeach
                    </select>
                    @error('IDLote')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Alimento</label>
                    <select name="IDTipoAlimento" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Seleccione tipo</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->IDTipoAlimento }}" {{ old('IDTipoAlimento') == $tipo->IDTipoAlimento ? 'selected' : '' }}>{{ $tipo->Nombre }}</option>
                        @endforeach
                    </select>
                    @error('IDTipoAlimento')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad (kg)</label>
                        <input type="number" step="0.01" name="CantidadKg" value="{{ old('CantidadKg') }}" min="0" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('CantidadKg')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea name="Observaciones" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('Observaciones') }}</textarea>
                    @error('Observaciones')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('admin.alimentacion.index') }}" class="px-4 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">Cancelar</a>
                    <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
