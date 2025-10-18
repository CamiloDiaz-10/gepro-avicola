@extends('layouts.app-with-sidebar')

@section('content')
<div class="p-6">
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Lote</h1>
            <p class="text-gray-600 dark:text-gray-300">Actualiza la información del lote</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.lotes.update', $lote) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Finca</label>
                    <select name="IDFinca" class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @foreach($fincas as $finca)
                            <option value="{{ $finca->IDFinca }}" {{ old('IDFinca', $lote->IDFinca) == $finca->IDFinca ? 'selected' : '' }}>
                                {{ $finca->Nombre }} ({{ $finca->Ubicacion }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre del Lote</label>
                    <input type="text" name="Nombre" value="{{ old('Nombre', $lote->Nombre) }}" class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Fecha de Ingreso</label>
                    <input type="date" name="FechaIngreso" value="{{ old('FechaIngreso', optional($lote->FechaIngreso)->format('Y-m-d')) }}" class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Cantidad Inicial</label>
                    <input type="number" name="CantidadInicial" value="{{ old('CantidadInicial', $lote->CantidadInicial) }}" min="1" class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Raza (opcional)</label>
                    <input type="text" name="Raza" value="{{ old('Raza', $lote->Raza) }}" class="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.lotes.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-200 dark:hover:bg-gray-500">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
