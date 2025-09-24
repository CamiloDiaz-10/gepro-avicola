@extends('layouts.app-with-sidebar')

@section('title', 'Nuevo Tratamiento - Sanidad')

@section('content')
<div class="p-6">
  <div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Registrar Tratamiento</h1>
      <a href="{{ route('admin.sanidad.index') }}" class="text-blue-600 hover:text-blue-800">Volver</a>
    </div>

    @if($errors->any())
      <div class="mb-4 p-3 bg-red-50 text-red-800 border border-red-200 rounded">
        <ul class="list-disc list-inside text-sm">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
      <form method="POST" action="{{ route('admin.sanidad.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Lote<span class="text-red-600">*</span></label>
            <select name="IDLote" class="w-full border-gray-300 rounded-md" required>
              <option value="">Seleccione...</option>
              @foreach($lotes as $l)
                <option value="{{ $l->IDLote }}" @selected(old('IDLote')==$l->IDLote)>{{ $l->Nombre }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Fecha<span class="text-red-600">*</span></label>
            <input type="date" name="Fecha" value="{{ old('Fecha', now()->toDateString()) }}" class="w-full border-gray-300 rounded-md" required />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Producto<span class="text-red-600">*</span></label>
            <input type="text" name="Producto" value="{{ old('Producto') }}" class="w-full border-gray-300 rounded-md" maxlength="100" required />
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Tipo de Tratamiento</label>
            @php $tipos = ['Vacuna','Desparasitante','Vitamina','Otro']; @endphp
            <select name="TipoTratamiento" class="w-full border-gray-300 rounded-md">
              <option value="">Seleccione...</option>
              @foreach($tipos as $tipo)
                <option value="{{ $tipo }}" @selected(old('TipoTratamiento')==$tipo)>{{ $tipo }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm text-gray-600 mb-1">Dosis</label>
          <input type="text" name="Dosis" value="{{ old('Dosis') }}" class="w-full border-gray-300 rounded-md" maxlength="50" />
        </div>

        <div>
          <label class="block text-sm text-gray-600 mb-1">Observaciones</label>
          <textarea name="Observaciones" rows="3" class="w-full border-gray-300 rounded-md">{{ old('Observaciones') }}</textarea>
        </div>

        <div class="pt-2">
          <button class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-md">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
