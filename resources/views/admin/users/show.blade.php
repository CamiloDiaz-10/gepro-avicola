@extends('layouts.app-with-sidebar')

@section('title', 'Detalles del Usuario - Gepro Avícola')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->Nombre }} {{ $user->Apellido }}</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">Información detallada del usuario</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.users.edit', $user) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-edit mr-2"></i>Editar
                    </a>
                    
                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="bg-{{ ($user->Estado ?? 'Activo') === 'Activo' ? 'yellow' : 'green' }}-600 hover:bg-{{ ($user->Estado ?? 'Activo') === 'Activo' ? 'yellow' : 'green' }}-700 text-white px-4 py-2 rounded-lg transition-colors"
                                onclick="return confirm('¿Estás seguro de cambiar el estado de este usuario?')">
                            <i class="fas fa-{{ ($user->Estado ?? 'Activo') === 'Activo' ? 'user-slash' : 'user-check' }} mr-2"></i>
                            {{ ($user->Estado ?? 'Activo') === 'Activo' ? 'Desactivar' : 'Activar' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- User Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información Personal</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Tipo de Identificación</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                @switch($user->TipoIdentificacion)
                                    @case('CC') Cédula de Ciudadanía @break
                                    @case('CE') Cédula de Extranjería @break
                                    @case('TI') Tarjeta de Identidad @break
                                    @case('PP') Pasaporte @break
                                    @default {{ $user->TipoIdentificacion }}
                                @endswitch
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Número de Identificación</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->NumeroIdentificacion }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nombre Completo</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->Nombre }} {{ $user->Apellido }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->Email }}</p>
                        </div>

                        @if($user->Telefono)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->Telefono }}</p>
                        </div>
                        @endif

                        @if($user->FechaNacimiento)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Nacimiento</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $user->FechaNacimiento->format('d/m/Y') }}
                                <span class="text-gray-500 dark:text-gray-400">({{ $user->FechaNacimiento->age }} años)</span>
                            </p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Rol</label>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $user->role->NombreRol === 'Administrador' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : 
                                   ($user->role->NombreRol === 'Propietario' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300') }}">
                                {{ $user->role->NombreRol }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Estado</label>
                            <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ ($user->Estado ?? 'Activo') === 'Activo' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                {{ $user->Estado ?? 'Activo' }}
                            </span>
                        </div>
                    </div>

                    @if($user->Direccion)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Dirección</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->Direccion }}</p>
                    </div>
                    @endif
                </div>

                <!-- Assigned Farms -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Fincas Asignadas</h3>
                    
                    @if($user->fincas->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($user->fincas as $finca)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $finca->Nombre }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $finca->Ubicacion }}</p>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 space-y-0.5">
                                @if(!is_null($finca->Hectareas))
                                    <p>Hectáreas: {{ number_format($finca->Hectareas, 2) }}</p>
                                @endif
                                @if(!is_null($finca->Latitud) && !is_null($finca->Longitud))
                                    <p>Coordenadas: {{ $finca->Latitud }}, {{ $finca->Longitud }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-warehouse fa-3x mb-4"></i>
                        <p>No tiene fincas asignadas</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistics and Actions -->
            <div class="space-y-6">
                <!-- User Statistics -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Estadísticas</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Fincas Asignadas</span>
                            <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $stats['fincas_asignadas'] }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Lotes en Fincas</span>
                            <span class="font-semibold text-green-600 dark:text-green-400">{{ $stats['lotes_en_fincas'] }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Registros de Producción</span>
                            <span class="font-semibold text-purple-600 dark:text-purple-400">{{ $stats['registros_produccion'] }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Registros de Sanidad</span>
                            <span class="font-semibold text-orange-600 dark:text-orange-400">{{ $stats['registros_sanidad'] }}</span>
                        </div>
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Último Acceso</span>
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $stats['ultimo_acceso'] ? $stats['ultimo_acceso']->diffForHumans() : 'Nunca' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Información de Cuenta</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de Registro</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Última Actualización</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acciones Rápidas</h3>
                    
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded transition-colors text-left"
                                    onclick="return confirm('¿Estás seguro de restablecer la contraseña de este usuario?')">
                                <i class="fas fa-key mr-2"></i>Restablecer Contraseña
                            </button>
                        </form>

                        @if($user->IDUsuario !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded transition-colors text-left"
                                    onclick="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                <i class="fas fa-trash mr-2"></i>Eliminar Usuario
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
