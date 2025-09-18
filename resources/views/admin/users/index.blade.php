@extends('layouts.app-with-sidebar')

@section('title', 'Gestión de Usuarios - Gepro Avícola')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Gestión de Usuarios</h1>
                    <p class="mt-2 text-gray-600">Administra todos los usuarios del sistema</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Nuevo Usuario
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input type="text" 
                           name="search" 
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="Nombre, email, identificación..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Rol</label>
                    <select name="role" id="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                    <a href="{{ route('admin.users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md transition-colors">
                        <i class="fas fa-times mr-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Usuarios ({{ $users->total() }})
                </h3>
            </div>

            @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Identificación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fincas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-sm font-medium text-blue-600">
                                                {{ strtoupper(substr($user->Nombre, 0, 1) . substr($user->Apellido, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $user->Nombre }} {{ $user->Apellido }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $user->Email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->TipoIdentificacion }}: {{ $user->NumeroIdentificacion }}</div>
                                @if($user->Telefono)
                                <div class="text-sm text-gray-500">{{ $user->Telefono }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $user->role->NombreRol === 'Administrador' ? 'bg-red-100 text-red-800' : 
                                       ($user->role->NombreRol === 'Propietario' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                    {{ $user->role->NombreRol }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->fincas->count() }} finca(s)</div>
                                @if($user->fincas->count() > 0)
                                <div class="text-xs text-gray-500">
                                    {{ $user->fincas->pluck('NombreFinca')->take(2)->implode(', ') }}
                                    @if($user->fincas->count() > 2)
                                        y {{ $user->fincas->count() - 2 }} más
                                    @endif
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ ($user->Estado ?? 'Activo') === 'Activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->Estado ?? 'Activo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <!-- Toggle Status -->
                                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="{{ ($user->Estado ?? 'Activo') === 'Activo' ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }}"
                                                title="{{ ($user->Estado ?? 'Activo') === 'Activo' ? 'Desactivar' : 'Activar' }}"
                                                onclick="return confirm('¿Estás seguro de cambiar el estado de este usuario?')">
                                            <i class="fas fa-{{ ($user->Estado ?? 'Activo') === 'Activo' ? 'user-slash' : 'user-check' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Reset Password -->
                                    <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-orange-600 hover:text-orange-900"
                                                title="Restablecer contraseña"
                                                onclick="return confirm('¿Estás seguro de restablecer la contraseña de este usuario?')">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    </form>

                                    @if($user->IDUsuario !== auth()->id())
                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900"
                                                title="Eliminar"
                                                onclick="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-users fa-3x text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron usuarios</h3>
                <p class="text-gray-500 mb-4">
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
