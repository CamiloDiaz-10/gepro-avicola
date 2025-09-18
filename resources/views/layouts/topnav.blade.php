<!-- Barra de Navegación Superior -->
<nav class="bg-white shadow-md">
    <div class="px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Botón de Menú Móvil -->
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-600 md:hidden">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <!-- Lado Derecho -->
            <div class="flex items-center space-x-4">
                <!-- Notificaciones -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 text-gray-500 hover:text-gray-600">
                        <i class="fas fa-bell"></i>
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">3</span>
                    </button>
                    <!-- Menú de Notificaciones -->
                    <div x-show="open" 
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-2 z-50">
                        <a href="#" class="block px-4 py-3 hover:bg-gray-100">
                            <p class="text-sm font-medium text-gray-900">Nueva producción registrada</p>
                            <p class="text-xs text-gray-500">Hace 5 minutos</p>
                        </a>
                        <a href="#" class="block px-4 py-3 hover:bg-gray-100">
                            <p class="text-sm font-medium text-gray-900">Alerta de nivel bajo de alimento</p>
                            <p class="text-xs text-gray-500">Hace 2 horas</p>
                        </a>
                    </div>
                </div>

                <!-- Menú de Usuario -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                        <img class="h-8 w-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}" alt="{{ Auth::user()->name }}">
                        <span class="hidden md:inline-block">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    <!-- Menú Desplegable de Usuario -->
                    <div x-show="open" 
                         @click.away="open = false"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                        <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-circle mr-2"></i> Mi Perfil
                        </a>
                        <a href="{{ route('profile.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i> Configuración
                        </a>
                        <hr class="my-2">
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 focus:outline-none focus:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>