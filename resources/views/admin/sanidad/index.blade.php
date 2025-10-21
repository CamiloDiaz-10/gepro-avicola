@extends('layouts.app-with-sidebar')

@section('title', 'Tratamientos (Sanidad) - Admin')

@section('content')
<div class="p-6">
  <div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tratamientos (Sanidad)</h1>
        <p class="text-gray-600 dark:text-gray-300">Gestione y filtre los registros de tratamientos</p>
      </div>
      @if(auth()->check() && method_exists(auth()->user(), 'hasFincasAsignadas') && auth()->user()->hasFincasAsignadas())
        <a href="{{ route(request()->routeIs('veterinario.*') ? 'veterinario.sanidad.create' : 'admin.sanidad.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
          <i class="fas fa-heartbeat mr-2"></i>Nuevo Tratamiento
        </a>
      @endif
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
      <form method="GET" action="{{ route(request()->routeIs('veterinario.*') ? 'veterinario.sanidad.index' : 'admin.sanidad.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Tipo</label>
          <select name="tipo" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
            <option value="">Todos</option>
            @php $tipos = ['Vacuna','Desparasitante','Vitamina','Otro']; @endphp
            @foreach($tipos as $tipo)
              <option value="{{ $tipo }}" @selected(request('tipo')===$tipo)>{{ $tipo }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Lote</label>
          <select name="lote" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
            <option value="">Todos</option>
            @foreach($lotes as $l)
              <option value="{{ $l->IDLote }}" @selected(request('lote')==$l->IDLote)>{{ $l->Nombre }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Desde</label>
          <input type="date" name="desde" value="{{ request('desde') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" />
        </div>
        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Hasta</label>
          <div class="flex gap-2">
            <input type="date" name="hasta" value="{{ request('hasta') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" />
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">Filtrar</button>
          </div>
        </div>
      </form>
    </div>

    @if(session('success'))
      <div class="mb-4 p-3 bg-green-50 text-green-800 border border-green-200 rounded">{{ session('success') }}</div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
      <table class="min-w-full">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Fecha</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Lote</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Producto</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tipo</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Dosis</th>
            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          @forelse($treatments as $t)
          <tr>
            <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200">{{ optional($t->Fecha)->format('Y-m-d') }}</td>
            <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200">{{ $t->lote->Nombre ?? '—' }}</td>
            <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200">{{ $t->Producto }}</td>
            <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200">{{ $t->TipoTratamiento ?? '—' }}</td>
            <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200">{{ $t->Dosis ?? '—' }}</td>
            <td class="px-4 py-2 text-sm text-right">
              <div class="inline-flex gap-2">
                <a href="{{ route(request()->routeIs('veterinario.*') ? 'veterinario.sanidad.edit' : 'admin.sanidad.edit', $t->IDSanidad) }}" class="text-blue-600 hover:text-blue-800">Editar</a>
                <form method="POST" action="{{ route(request()->routeIs('veterinario.*') ? 'veterinario.sanidad.destroy' : 'admin.sanidad.destroy', $t->IDSanidad) }}" onsubmit="return confirm('¿Eliminar este tratamiento?');">
                  @csrf
                  @method('DELETE')
                  <button class="text-red-600 hover:text-red-800">Eliminar</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No hay tratamientos registrados</td>
          </tr>
          @endforelse
        </tbody>
      </table>

      <div class="px-4 py-3">{{ $treatments->links() }}</div>
    </div>
  </div>
</div>
@endsection
