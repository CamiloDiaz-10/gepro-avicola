<!-- Sidebar -->
<aside class="bg-blue-900 text-white min-h-screen fixed left-0 top-0 transform transition-all duration-300 ease-in-out z-30"
       :class="{
           'translate-x-0': sidebarOpen || window.innerWidth >= 768,
           '-translate-x-full': !sidebarOpen && window.innerWidth < 768,
           'w-64': !sidebarCollapsed,
           'w-16': sidebarCollapsed && window.innerWidth >= 768
       }">
    <!-- Logo -->
    <div class="p-6 border-b border-blue-800">
        <h1 class="text-2xl font-bold transition-opacity duration-300" 
            :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0' : 'opacity-100'">
            Gepro Avícola
        </h1>
        <!-- Logo colapsado -->
        <div class="text-center" 
             x-show="sidebarCollapsed && window.innerWidth >= 768" 
             x-transition>
            <i class="fas fa-feather text-2xl"></i>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="p-4">
        <ul class="space-y-2">
            <li>
                @php
                    $dashboardRoute = 'dashboard';
                    $isActive = false;
                    
                    if (auth()->check() && auth()->user()->role) {
                        switch (auth()->user()->role->NombreRol) {
                            case 'Administrador':
                                $dashboardRoute = 'admin.dashboard';
                                $isActive = request()->routeIs('admin.dashboard');
                                break;
                            case 'Propietario':
                                $dashboardRoute = 'owner.dashboard';
                                $isActive = request()->routeIs('owner.dashboard');
                                break;
                            case 'Empleado':
                                $dashboardRoute = 'employee.dashboard';
                                $isActive = request()->routeIs('employee.dashboard');
                                break;
                            default:
                                $isActive = request()->routeIs('dashboard');
                        }
                    }
                @endphp
                
                <a href="{{ route($dashboardRoute) }}" 
                   class="flex items-center p-3 text-white rounded hover:bg-blue-800 transition-all duration-200 relative tooltip {{ $isActive ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-home" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-3'"></i>
                    <span class="transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Inicio
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Inicio</span>
                </a>
            </li>

            <!-- Opciones específicas para Administradores -->
            @if(auth()->check() && auth()->user()->role && auth()->user()->role->NombreRol === 'Administrador')
            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Administración
                </div>
            </li>

            <li>
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center p-3 text-white rounded hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.users.*') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-users" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-3'"></i>
                    <span class="transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Gestionar Usuarios
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Gestionar Usuarios</span>
                </a>
            </li>

            <li>
                <a href="#" 
                   class="flex items-center p-3 text-white rounded hover:bg-blue-800 transition-all duration-200 relative tooltip"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''"
                   onclick="alert('Próximamente: Gestión de Fincas')">
                    <i class="fas fa-warehouse" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-3'"></i>
                    <span class="transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Gestionar Fincas
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Gestionar Fincas</span>
                </a>
            </li>

            <li>
                <a href="#" 
                   class="flex items-center p-3 text-white rounded hover:bg-blue-800 transition-all duration-200 relative tooltip"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''"
                   onclick="alert('Próximamente: Gestión de Lotes')">
                    <i class="fas fa-layer-group" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-3'"></i>
                    <span class="transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Gestionar Lotes
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Gestionar Lotes</span>
                </a>
            </li>

            <li>
                <a href="#" 
                   class="flex items-center p-3 text-white rounded hover:bg-blue-800 transition-all duration-200 relative tooltip"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''"
                   onclick="alert('Próximamente: Sistema de Reportes')">
                    <i class="fas fa-chart-bar" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-3'"></i>
                    <span class="transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Reportes Avanzados
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Reportes Avanzados</span>
                </a>
            </li>

            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Operaciones
                </div>
            </li>
            @endif

            <li>
                <a href="#" 
                   class="flex items-center p-3 text-white rounded hover:bg-blue-800 transition-all duration-200 relative tooltip"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''"
                   onclick="alert('Módulo de Fincas en desarrollo')">
                    <i class="fas fa-warehouse" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-3'"></i>
                    <span class="transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Fincas
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Fincas</span>
                </a>
            </li>

            <li>
                <a href="#" 
                   class="flex items-center p-3 text-white rounded hover:bg-blue-800 transition-all duration-200 relative tooltip"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''"
                   onclick="alert('Módulo de Aves en desarrollo')">
                    <i class="fas fa-feather" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-3'"></i>
                    <span class="transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Aves
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Aves</span>
                </a>
            </li>

            @can('Propietario')
            <li>
                <a href="#" 
                   class="flex items-center p-3 text-white rounded hover:bg-blue-800 transition-all duration-200 relative tooltip"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''"
                   onclick="alert('Módulo de Producción en desarrollo')">
                    <i class="fas fa-egg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-3'"></i>
                    <span class="transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Producción de Huevos
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Producción de Huevos</span>
                </a>
            </li>

            <li>
                <a href="#" 
                   class="flex items-center p-3 text-white rounded hover:bg-blue-800 transition-all duration-200 relative tooltip"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''"
                   onclick="alert('Módulo de Reportes en desarrollo')">
                    <i class="fas fa-chart-line" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-3'"></i>
                    <span class="transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Informes y Reportes
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Informes y Reportes</span>
                </a>
            </li>
            @endcan

            <li>
                <a href="{{ route('profile.index') }}" 
                   class="flex items-center p-3 text-white rounded hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('profile.*') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-user-circle" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-3'"></i>
                    <span class="transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Mi Perfil
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Mi Perfil</span>
                </a>
            </li>
            
            <!-- Ejemplo de sincronización -->
            <li>
                <a href="{{ route('example.sidebar') }}" 
                   class="flex items-center p-3 text-white rounded hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('example.sidebar') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-cogs" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-3'"></i>
                    <span class="transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Demo Sincronización
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Demo Sincronización</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Mobile Sidebar Overlay -->
<div x-show="sidebarOpen && window.innerWidth < 768" 
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>

<!-- Estilos para tooltips -->
<style>
    .tooltip {
        position: relative;
    }
    
    .tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
    
    .tooltip-text {
        visibility: hidden;
        opacity: 0;
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        margin-left: 15px;
        background-color: #374151;
        color: white;
        text-align: center;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 14px;
        white-space: nowrap;
        z-index: 1000;
        transition: opacity 0.3s;
    }
    
    .tooltip-text::before {
        content: "";
        position: absolute;
        top: 50%;
        left: -5px;
        transform: translateY(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: transparent #374151 transparent transparent;
    }
</style>