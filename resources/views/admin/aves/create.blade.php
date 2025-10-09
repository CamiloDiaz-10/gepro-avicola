@extends('layouts.app-with-sidebar')

@section('title', 'Registrar Ave')

@section('content')
<div class="p-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-800">Registrar Nueva Ave</h1>
                <a href="{{ route('admin.aves.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Ver Aves</a>
            </div>

            @if(session('error'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-700">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.aves.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto del Ave (opcional)</label>
                    <input type="file" name="Foto" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('Foto')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Gallina</label>
                    <select name="IDTipoGallina" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Seleccione tipo</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->IDTipoGallina }}" {{ old('IDTipoGallina') == $tipo->IDTipoGallina ? 'selected' : '' }}>{{ $tipo->Nombre }}</option>
                        @endforeach
                    </select>
                    @error('IDTipoGallina')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                        <input type="date" name="FechaNacimiento" value="{{ old('FechaNacimiento', $hoy) }}" max="{{ date('Y-m-d') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('FechaNacimiento')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Peso (g)</label>
                        <input type="number" step="0.01" name="Peso" value="{{ old('Peso') }}" min="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('Peso')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="Estado" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="Viva" {{ old('Estado') == 'Viva' ? 'selected' : '' }}>Viva</option>
                            <option value="Muerta" {{ old('Estado') == 'Muerta' ? 'selected' : '' }}>Muerta</option>
                            <option value="Vendida" {{ old('Estado') == 'Vendida' ? 'selected' : '' }}>Vendida</option>
                            <option value="Trasladada" {{ old('Estado') == 'Trasladada' ? 'selected' : '' }}>Trasladada</option>
                        </select>
                        @error('Estado')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('admin.aves.index') }}" class="px-4 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">Cancelar</a>
                    <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
