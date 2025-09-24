@extends('layouts.app-with-sidebar')

@section('content')
<div class="p-6">
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Finca</h1>
            <p class="text-gray-600">Actualiza la información de la finca</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.fincas.update', $finca) }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" name="Nombre" value="{{ old('Nombre', $finca->Nombre) }}" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ubicación</label>
                    <input type="text" name="Ubicacion" value="{{ old('Ubicacion', $finca->Ubicacion) }}" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Latitud</label>
                    <input type="number" step="0.000001" name="Latitud" value="{{ old('Latitud', $finca->Latitud) }}" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Longitud</label>
                    <input type="number" step="0.000001" name="Longitud" value="{{ old('Longitud', $finca->Longitud) }}" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hectáreas</label>
                    <input type="number" step="0.01" name="Hectareas" value="{{ old('Hectareas', $finca->Hectareas) }}" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.fincas.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
