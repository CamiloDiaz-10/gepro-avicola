@extends('layouts.app-with-sidebar')

@section('title', 'Registrar Producción de Hoy')

@section('content')
<div class="p-6" x-data="produccionForm()">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            @php
                $current = Route::currentRouteName();
                $area = \Illuminate\Support\Str::startsWith($current, 'owner.') ? 'owner' : (\Illuminate\Support\Str::startsWith($current, 'employee.') ? 'employee' : 'admin');
            @endphp
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Registrar Producción de Huevos (Hoy)</h1>
                <a href="{{ route($area.'.produccion-huevos.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">Ver Reportes</a>
            </div>

            @if(session('error'))
                <div class="mb-4 p-3 rounded bg-red-100 text-red-700">{{ session('error') }}</div>
            @endif

            <form action="{{ route($area.'.produccion-huevos.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha</label>
                    <input type="date" name="Fecha" value="{{ old('Fecha', $hoy) }}" max="{{ date('Y-m-d') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('Fecha')<p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lote (Solo lotes ponedores)</label>
                    <select name="IDLote" 
                            required 
                            x-model="loteSeleccionado"
                            @change="cargarInfoLote()"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">Seleccione lote</option>
                        @foreach($lotes as $lote)
                            @php
                                $loteId = is_array($lote) ? $lote['IDLote'] : $lote->IDLote;
                                $loteNombre = is_array($lote) ? $lote['Nombre'] : $lote->Nombre;
                                $loteTipo = is_array($lote) && isset($lote['tipo']) ? $lote['tipo'] : (is_object($lote) && $lote->tipo_predominante ? $lote->tipo_predominante->Nombre : '');
                            @endphp
                            <option value="{{ $loteId }}" {{ old('IDLote') == $loteId ? 'selected' : '' }}>
                                {{ $loteNombre }}
                                @if($loteTipo)
                                    ({{ $loteTipo }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('IDLote')<p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Los lotes de engorde no aparecen porque no producen huevos</p>
                </div>

                <!-- Información del Lote Seleccionado -->
                <div x-show="loteInfo" 
                     x-transition
                     class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2">📊 Información del Lote</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Tipo de Gallina:</span>
                            <span class="font-semibold text-gray-900 dark:text-white ml-1" x-text="loteInfo?.tipo_gallina || 'N/A'"></span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Aves Activas:</span>
                            <span class="font-semibold text-gray-900 dark:text-white ml-1" x-text="loteInfo?.aves_activas || '0'"></span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Promedio Esperado:</span>
                            <span class="font-semibold text-gray-900 dark:text-white ml-1" x-text="loteInfo?.produccion_promedio || '0'"></span> huevos/día
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Huevos/Ave:</span>
                            <span class="font-semibold text-purple-700 dark:text-purple-400 ml-1" x-text="loteInfo?.huevos_por_ave_promedio || '0'"></span> por ave
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-600 dark:text-gray-400">Rango Esperado:</span>
                            <span class="font-semibold text-green-700 dark:text-green-400 ml-1">
                                <span x-text="loteInfo?.produccion_minima || '0'"></span> - 
                                <span x-text="loteInfo?.produccion_maxima || '0'"></span> huevos
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                                (<span x-text="loteInfo?.huevos_por_ave_min || '0'"></span>-<span x-text="loteInfo?.huevos_por_ave_max || '0'"></span> huevos/ave según tipo)
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cantidad de Huevos</label>
                        <input type="number" 
                               name="CantidadHuevos" 
                               x-model="cantidadHuevos"
                               @input="validarCantidad()"
                               value="{{ old('CantidadHuevos') }}" 
                               min="0" 
                               required
                               :class="{
                                   'border-red-500 dark:border-red-400': validacionCantidad === 'error',
                                   'border-yellow-500 dark:border-yellow-400': validacionCantidad === 'warning',
                                   'border-green-500 dark:border-green-400': validacionCantidad === 'success'
                               }"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        
                        <!-- Mensaje de validación en tiempo real -->
                        <div x-show="mensajeValidacion" 
                             x-transition
                             class="mt-1 text-xs"
                             :class="{
                                 'text-red-600 dark:text-red-400': validacionCantidad === 'error',
                                 'text-yellow-600 dark:text-yellow-400': validacionCantidad === 'warning',
                                 'text-green-600 dark:text-green-400': validacionCantidad === 'success'
                             }"
                             x-text="mensajeValidacion"></div>
                        
                        @error('CantidadHuevos')<p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Huevos Rotos</label>
                        <input type="number" name="HuevosRotos" value="{{ old('HuevosRotos', 0) }}" min="0"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('HuevosRotos')<p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Turno</label>
                        <select name="Turno" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Sin especificar</option>
                            <option value="Mañana" {{ old('Turno') == 'Mañana' ? 'selected' : '' }}>Mañana</option>
                            <option value="Tarde" {{ old('Turno') == 'Tarde' ? 'selected' : '' }}>Tarde</option>
                            <option value="Noche" {{ old('Turno') == 'Noche' ? 'selected' : '' }}>Noche</option>
                        </select>
                        @error('Turno')<p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Peso Promedio (Lb)</label>
                    <input type="number" step="0.01" name="PesoPromedio" value="{{ old('PesoPromedio') }}" min="0"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @error('PesoPromedio')<p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observaciones</label>
                    <textarea name="Observaciones" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('Observaciones') }}</textarea>
                    @error('Observaciones')<p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route($area.'.produccion-huevos.index') }}" class="px-4 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Cancelar</a>
                    <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function produccionForm() {
    return {
        loteSeleccionado: '{{ old('IDLote') }}',
        cantidadHuevos: '{{ old('CantidadHuevos') }}',
        loteInfo: null,
        validacionCantidad: null,
        mensajeValidacion: '',
        cargando: false,

        init() {
            // Si hay un lote seleccionado (por old()), cargar su info
            if (this.loteSeleccionado) {
                this.cargarInfoLote();
            }
        },

        async cargarInfoLote() {
            if (!this.loteSeleccionado) {
                this.loteInfo = null;
                this.validacionCantidad = null;
                this.mensajeValidacion = '';
                return;
            }

            this.cargando = true;
            
            try {
                const response = await fetch(`/api/lotes/${this.loteSeleccionado}/produccion-info`);
                const data = await response.json();
                
                if (data.success) {
                    this.loteInfo = data.data;
                    // Validar cantidad si ya hay una ingresada
                    if (this.cantidadHuevos) {
                        this.validarCantidad();
                    }
                } else {
                    this.loteInfo = null;
                    // Si es lote de engorde, mostrar mensaje específico
                    if (data.es_engorde) {
                        alert('⚠️ Este lote es de aves de engorde y no producen huevos. Por favor selecciona otro lote.');
                        this.loteSeleccionado = '';
                    } else {
                        alert(data.message || 'Error al cargar información del lote');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar información del lote');
            } finally {
                this.cargando = false;
            }
        },

        validarCantidad() {
            if (!this.loteInfo || !this.cantidadHuevos) {
                this.validacionCantidad = null;
                this.mensajeValidacion = '';
                return;
            }

            const cantidad = parseInt(this.cantidadHuevos);
            const minimo = this.loteInfo.produccion_minima;
            const promedio = this.loteInfo.produccion_promedio;
            const maximo = this.loteInfo.produccion_maxima;

            if (cantidad > maximo) {
                this.validacionCantidad = 'error';
                this.mensajeValidacion = `⚠️ La cantidad excede el máximo posible (${maximo} huevos)`;
            } else if (cantidad < minimo) {
                this.validacionCantidad = 'warning';
                this.mensajeValidacion = `⚠️ Cantidad por debajo del mínimo esperado (${minimo} huevos)`;
            } else if (cantidad >= promedio - (promedio * 0.1) && cantidad <= promedio + (promedio * 0.1)) {
                this.validacionCantidad = 'success';
                this.mensajeValidacion = `✓ Cantidad dentro del rango promedio esperado`;
            } else {
                this.validacionCantidad = 'success';
                this.mensajeValidacion = `✓ Cantidad válida (${minimo} - ${maximo} huevos)`;
            }
        }
    }
}
</script>
@endsection
