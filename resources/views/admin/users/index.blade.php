@extends('layouts.app-with-sidebar')

@section('title', 'Gestión de Usuarios - Gepro Avícola')

@push('styles')
<style>
    /* Scrollbar personalizado para la tabla */
    .overflow-x-auto {
        scrollbar-width: thin;
        scrollbar-color: #9ca3af #e5e7eb;
    }
    
    .dark .overflow-x-auto {
        scrollbar-color: #4b5563 #1f2937;
    }
    
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #e5e7eb;
        border-radius: 4px;
    }
    
    .dark .overflow-x-auto::-webkit-scrollbar-track {
        background: #1f2937;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #9ca3af;
        border-radius: 4px;
    }
    
    .dark .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #4b5563;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }
    
    .dark .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }
</style>
@endpush

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Gestión de Usuarios</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">Administra todos los usuarios del sistema</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nuevo Usuario
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Buscar</label>
                    <input type="text" 
                           name="search" 
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="Nombre, email, identificación..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Rol</label>
                    <select name="role" id="role" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->IDRol }}" {{ request('role') == $role->IDRol ? 'selected' : '' }}>
                            {{ $role->NombreRol }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md transition-colors mr-2">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-md transition-colors">
                        <i class="fas fa-times mr-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Usuarios ({{ $users->total() }})
                </h3>
            </div>

            @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Identificación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fincas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                {{ strtoupper(substr($user->Nombre, 0, 1) . substr($user->Apellido, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $user->Nombre }} {{ $user->Apellido }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->Email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $user->TipoIdentificacion }}: {{ $user->NumeroIdentificacion }}</div>
                                @if($user->Telefono)
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->Telefono }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $user->role->NombreRol === 'Administrador' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : 
                                       ($user->role->NombreRol === 'Propietario' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300') }}">
                                    {{ $user->role->NombreRol }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $user->fincas->count() }} finca(s)</div>
                                @if($user->fincas->count() > 0)
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $user->fincas->pluck('Nombre')->take(2)->implode(', ') }}
                                    @if($user->fincas->count() > 2)
                                        y {{ $user->fincas->count() - 2 }} más
                                    @endif
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ ($user->Estado ?? 'Activo') === 'Activo' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                    {{ $user->Estado ?? 'Activo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <!-- Ver detalles -->
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors" 
                                       title="Ver detalles">
                                        <i class="fas fa-eye text-lg"></i>
                                    </a>
                                    
                                    <!-- Editar -->
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors" 
                                       title="Editar">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>
                                    
                                    <!-- Toggle Status (Activar/Desactivar) -->
                                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-orange-500 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 transition-colors"
                                                title="{{ ($user->Estado ?? 'Activo') === 'Activo' ? 'Desactivar usuario' : 'Activar usuario' }}"
                                                onclick="return confirm('¿Estás seguro de cambiar el estado de este usuario?')">
                                            <i class="fas fa-user-slash text-lg"></i>
                                        </button>
                                    </form>

                                    <!-- Reset Password -->
                                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 transition-colors"
                                                title="Restablecer contraseña"
                                                onclick="return confirm('¿Estás seguro de restablecer la contraseña de este usuario? La nueva contraseña será: password123')">
                                            <i class="fas fa-key text-lg"></i>
                                        </button>
                                    </form>

                                    @if($user->IDUsuario !== auth()->id())
                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors"
                                                title="Eliminar usuario"
                                                onclick="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                            <i class="fas fa-trash text-lg"></i>
                                        </button>
                                    </form>
                                    @else
                                    <!-- Placeholder para mantener alineación -->
                                    <span class="inline-block w-5"></span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $users->links() }}
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-users fa-3x text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No se encontraron usuarios</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    @if(request()->hasAny(['search', 'role']))
                        No hay usuarios que coincidan con los filtros aplicados.
                    @else
                        Comienza creando tu primer usuario.
                    @endif
                </p>
                @if(!request()->hasAny(['search', 'role']))
                <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Crear Usuario
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-submit form on filter change
    document.getElementById('role').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endpush
