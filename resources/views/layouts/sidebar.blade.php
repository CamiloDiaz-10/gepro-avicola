<!-- Sidebar -->
<aside class="bg-blue-900 dark:bg-gray-800 text-white h-screen fixed left-0 top-0 transform transition-all duration-300 ease-in-out z-30 flex flex-col overflow-y-hidden"
       :class="{
           'translate-x-0': sidebarOpen || window.innerWidth >= 768,
           '-translate-x-full': !sidebarOpen && window.innerWidth < 768,
           'w-64': !sidebarCollapsed,
           'w-16': sidebarCollapsed && window.innerWidth >= 768
       }">
    <!-- Logo -->
    <div class="p-4 sm:p-6 border-b border-blue-800 dark:border-gray-700">
        <h1 class="text-xl sm:text-2xl font-bold transition-opacity duration-300" 
            :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0' : 'opacity-100'">
            Gepro Avícola
        </h1>
        <!-- Logo colapsado -->
        <div class="text-center" 
             x-show="sidebarCollapsed && window.innerWidth >= 768" 
             x-transition>
            <i class="fas fa-feather text-xl sm:text-2xl"></i>
        </div>
    </div>

    <!-- Navigation (scrollable area) -->
    <nav class="p-3 sm:p-4 flex-1 overflow-y-auto thin-scroll pr-2">
        <ul class="space-y-1 sm:space-y-2">
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
                            case 'Veterinario':
                                $dashboardRoute = 'veterinario.dashboard';
                                $isActive = request()->routeIs('veterinario.dashboard');
                                break;
                            default:
                                $isActive = request()->routeIs('dashboard');
                        }
                    }
                @endphp
                
                <a href="{{ route($dashboardRoute) }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ $isActive ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-home text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Inicio
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Inicio</span>
                </a>
            </li>

            <!-- Opciones específicas para Administradores -->
            @if(auth()->check() && auth()->user()->role && auth()->user()->role->NombreRol === 'Administrador')
            <li>
                <div class="px-2 sm:px-3 py-1.5 sm:py-2 text-xs font-semibold text-blue-300 dark:text-gray-400 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Administración
                </div>
            </li>

            <li>
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.users.*') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-users text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Gestionar Usuarios
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Gestionar Usuarios</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.fincas.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.fincas.*') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-warehouse text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Gestionar Fincas
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Gestionar Fincas</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.lotes.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.lotes.*') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-layer-group text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Gestionar Lotes
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Gestionar Lotes</span>
                </a>
            </li>

            <!-- Gestión de Aves (Admin) -->
            <li>
                <a href="{{ route('admin.aves.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.aves.index') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-dove text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Gestionar Aves
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Gestionar Aves</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.aves.create') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.aves.create') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-plus-circle text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Registrar Ave
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Registrar Ave</span>
                </a>
            </li>

            <!-- Galería de Aves -->
            <li>
                <a href="{{ route('admin.aves.gallery') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.aves.gallery') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-images text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Galería de Aves
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Galería de Aves</span>
                </a>
            </li>

            <!-- Gestión de Alimentación (Admin) -->
            <li>
                <a href="{{ route('admin.alimentacion.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.alimentacion.index') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-utensils text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Gestión de Alimentación
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Gestión de Alimentación</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.alimentacion.create') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.alimentacion.create') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-plus-circle text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Registrar Alimentación
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Registrar Alimentación</span>
                </a>
            </li>

            <!-- Gestión de Tratamientos / Sanidad (Admin) -->
            <li>
                <a href="{{ route('admin.sanidad.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.sanidad.index') || request()->routeIs('admin.sanidad.edit') || request()->routeIs('admin.sanidad.show') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-notes-medical text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Tratamientos (Sanidad)
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Tratamientos (Sanidad)</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.sanidad.create') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.sanidad.create') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-plus-circle text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Registrar Tratamiento
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Registrar Tratamiento</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.reports.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.reports.*') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-chart-bar text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Reportes Avanzados
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Reportes Avanzados</span>
                </a>
            </li>

            <li>
                
            
            <!-- Producción de Huevos: Reportes -->
            <li>
                <a href="{{ route('admin.produccion-huevos.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.produccion-huevos.index') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-egg text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Producción de Huevos
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Producción de Huevos</span>
                </a>
            </li>

            <!-- Producción de Huevos: Registrar -->
            <li>
                <a href="{{ route('admin.produccion-huevos.create') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('admin.produccion-huevos.create') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-plus-circle text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Registrar Producción
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Registrar Producción</span>
                </a>
            </li>
            @endif

            <!-- Opciones específicas para Propietarios -->
            @if(auth()->check() && auth()->user()->role && auth()->user()->role->NombreRol === 'Propietario')
            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 dark:text-gray-400 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Mis Fincas
                </div>
            </li>
            <li>
                <a href="{{ route('owner.lotes.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('owner.lotes.index') || request()->routeIs('owner.lotes.show') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-layer-group text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Gestionar Lotes
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Gestionar Lotes</span>
                </a>
            </li>
            <li>
                <a href="{{ route('owner.lotes.create') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('owner.lotes.create') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-plus-circle text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Crear Lote
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Crear Lote</span>
                </a>
            </li>
            
            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 dark:text-gray-400 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Producción
                </div>
            </li>
            <li>
                <a href="{{ route('owner.produccion-huevos.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('owner.produccion-huevos.index') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-egg text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Producción de Huevos
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Producción de Huevos</span>
                </a>
            </li>
            <li>
                <a href="{{ route('owner.produccion-huevos.create') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('owner.produccion-huevos.create') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-plus-circle text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Registrar Producción
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Registrar Producción</span>
                </a>
            </li>

            <!-- Owner: Aves -->
            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 dark:text-gray-400 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Aves
                </div>
            </li>
            <li>
                <a href="{{ route('owner.aves.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('owner.aves.index') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-dove text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Mis Aves
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Mis Aves</span>
                </a>
            </li>
            <li>
                <a href="{{ route('owner.aves.create') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('owner.aves.create') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-plus-circle text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Registrar Ave
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Registrar Ave</span>
                </a>
            </li>

            <!-- Owner: Reportes -->
            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 dark:text-gray-400 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Reportes
                </div>
            </li>
            <li>
                <a href="{{ route('owner.reports.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('owner.reports.*') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-chart-bar text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Reportes
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Reportes</span>
                </a>
            </li>
            @endif

            <!-- Opciones específicas para Empleados -->
            @if(auth()->check() && auth()->user()->role && auth()->user()->role->NombreRol === 'Empleado')
            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 dark:text-gray-400 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Mis Fincas
                </div>
            </li>
            <li>
                <a href="{{ route('employee.lotes.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('employee.lotes.*') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-layer-group text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Mis Lotes
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Mis Lotes</span>
                </a>
            </li>
            
            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 dark:text-gray-400 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Producción
                </div>
            </li>
            <li>
                <a href="{{ route('employee.produccion-huevos.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('employee.produccion-huevos.index') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-egg text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Producción de Huevos
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Producción de Huevos</span>
                </a>
            </li>
            <li>
                <a href="{{ route('employee.produccion-huevos.create') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('employee.produccion-huevos.create') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-plus-circle text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Registrar Producción
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Registrar Producción</span>
                </a>
            </li>

            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 dark:text-gray-400 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Fincas
                </div>
            </li>
            <li>
                @php($empFarmsCount = auth()->check() ? optional(auth()->user()->fincas())->count() : 0)
                <a href="{{ route('employee.fincas.index') }}" 
                   class="flex items-center justify-between p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative {{ request()->routeIs('employee.fincas.*') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <div class="flex items-center">
                        <i class="fas fa-warehouse text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                        <span class="text-sm sm:text-base transition-opacity duration-300" 
                              :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                            Mis Fincas
                        </span>
                    </div>
                    <span class="ml-2 inline-flex items-center justify-center text-xs font-semibold bg-blue-700 rounded-full px-2 py-0.5"
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                        {{ $empFarmsCount }}
                    </span>
                </a>
            </li>

            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 dark:text-gray-400 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Reportes
                </div>
            </li>
            <li>
                <a href="{{ route('employee.reports.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('employee.reports.*') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-chart-bar text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Reportes
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Reportes</span>
                </a>
            </li>
            @endif

            <!-- Opciones específicas para Veterinarios -->
            @if(auth()->check() && auth()->user()->role && auth()->user()->role->NombreRol === 'Veterinario')
            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 dark:text-gray-400 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Alimentación
                </div>
            </li>
            <li>
                <a href="{{ route('veterinario.alimentacion.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('veterinario.alimentacion.index') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-drumstick-bite text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Gestión de Alimentación
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Gestión de Alimentación</span>
                </a>
            </li>
            <li>
                <a href="{{ route('veterinario.alimentacion.create') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('veterinario.alimentacion.create') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-plus-circle text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Registrar Alimentación
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Registrar Alimentación</span>
                </a>
            </li>

            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 dark:text-gray-400 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Aves
                </div>
            </li>
            <li>
                <a href="{{ route('veterinario.aves.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('veterinario.aves.index') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-dove text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Gestión de Aves
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Gestión de Aves</span>
                </a>
            </li>

            <!-- Veterinario: Lotes (solo lectura) -->
            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 dark:text-gray-400 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Lotes
                </div>
            </li>
            <li>
                <a href="{{ route('veterinario.lotes.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('veterinario.lotes.index') || request()->routeIs('veterinario.lotes.show') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-layer-group text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Ver Lotes
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Ver Lotes</span>
                </a>
            </li>

            <!-- Veterinario: Sanidad / Tratamientos -->
            <li>
                <div class="px-3 py-2 text-xs font-semibold text-blue-300 dark:text-gray-400 uppercase tracking-wider"
                     :class="sidebarCollapsed && window.innerWidth >= 768 ? 'hidden' : ''">
                    Sanidad
                </div>
            </li>
            <li>
                <a href="{{ route('veterinario.sanidad.index') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('veterinario.sanidad.index') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-notes-medical text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Tratamientos de Salud
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Tratamientos de Salud</span>
                </a>
            </li>
            <li>
                <a href="{{ route('veterinario.sanidad.create') }}" 
                   class="flex items-center p-2.5 sm:p-3 text-white rounded-md hover:bg-blue-800 transition-all duration-200 relative tooltip {{ request()->routeIs('veterinario.sanidad.create') ? 'bg-blue-800' : '' }}"
                   :class="sidebarCollapsed && window.innerWidth >= 768 ? 'justify-center' : ''">
                    <i class="fas fa-plus-circle text-base sm:text-lg" :class="sidebarCollapsed && window.innerWidth >= 768 ? '' : 'mr-2 sm:mr-3'"></i>
                    <span class="text-sm sm:text-base transition-opacity duration-300" 
                          :class="sidebarCollapsed && window.innerWidth >= 768 ? 'opacity-0 w-0 overflow-hidden' : 'opacity-100'">
                        Registrar Tratamiento
                    </span>
                    <span class="tooltip-text" x-show="sidebarCollapsed && window.innerWidth >= 768">Registrar Tratamiento</span>
                </a>
            </li>
            @endif
          
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
        margin-left: 10px;
        background-color: #374151;
        color: white;
        text-align: center;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 13px;
        white-space: nowrap;
        z-index: 1000;
        transition: opacity 0.3s;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
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

    /* Thin scrollbar for sidebar scroll area */
    .thin-scroll {
        scrollbar-width: thin; /* Firefox */
        scrollbar-color: #1f2937 transparent; /* Firefox */
    }
    .thin-scroll::-webkit-scrollbar {
        width: 8px; /* Chrome/Edge */
    }
    .thin-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .thin-scroll::-webkit-scrollbar-thumb {
        background-color: #1f2937; /* gray-800 */
        border-radius: 8px;
        border: 2px solid transparent;
        background-clip: padding-box;
    }
</style>