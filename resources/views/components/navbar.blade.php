@props([
    'title' => 'Gepro Avícola',
    'showUserInfo' => true,
    'showHamburger' => true
])

<nav class="bg-white dark:bg-gray-800 shadow-lg border-b border-gray-200 dark:border-gray-700 fixed top-0 right-0 z-40 transition-all duration-300 ease-in-out"
     :class="{
         'left-0': window.innerWidth < 768,
         'left-64': !sidebarCollapsed && window.innerWidth >= 768,
         'left-16': sidebarCollapsed && window.innerWidth >= 768
     }">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Hamburger button y Logo -->
            <div class="flex items-center">
                @if($showHamburger)
                <!-- Botón hamburguesa para móvil -->
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="md:hidden mr-3 p-2 rounded-md text-gray-400 dark:text-gray-300 hover:text-gray-500 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                    <span class="sr-only">Abrir menú</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                
                <!-- Botón hamburguesa para desktop (sidebar colapsible) -->
                <button @click="sidebarCollapsed = !sidebarCollapsed" 
                        class="hidden md:block mr-3 p-2 rounded-md text-gray-400 dark:text-gray-300 hover:text-gray-500 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                    <span class="sr-only">Toggle sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                @endif
                
                <div class="flex-shrink-0 flex items-center">
                    <img src="{{ asset('images/home2.png') }}" alt="Logo" class="w-10 h-10 sm:w-12 sm:h-12">
                    <div class="ml-2">
                        <h1 class="text-base sm:text-lg md:text-xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
                    </div>
                </div>
            </div>

            <!-- Información del usuario y logout -->
            @if($showUserInfo && auth()->check())
            <div class="flex items-center space-x-2 sm:space-x-4">
                <!-- Theme Toggle -->
                <x-theme-toggle />
                <!-- Información del usuario (oculta en móvil) -->
                <div class="hidden sm:flex items-center space-x-3">
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ auth()->user()->Nombre }} {{ auth()->user()->Apellido }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-300">
                            {{ auth()->user()->getRoleName() ?? 'Sin rol asignado' }}
                        </div>
                    </div>
                    
                    <!-- Avatar -->
                    <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Avatar solo para móvil -->
                <div class="sm:hidden w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>

                <!-- Dropdown menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition class="origin-top-right absolute right-0 mt-2 w-56 sm:w-48 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <!-- Información del usuario solo en móvil -->
                            <div class="sm:hidden px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ auth()->user()->Nombre }} {{ auth()->user()->Apellido }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-300 mt-1">
                                    {{ auth()->user()->getRoleName() ?? 'Sin rol asignado' }}
                                </div>
                            </div>
                            
                            <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Mi Perfil
                            </a>
                            <a href="{{ route('profile.settings') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Configuración
                            </a>
                            <hr class="my-1">
                            <div class="px-4 py-2">
                                <x-logout-button 
                                    size="small" 
                                    class="w-full justify-center bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out text-sm"
                                    confirmMessage="¿Estás seguro de que deseas cerrar sesión?"
                                >
                                    Cerrar Sesión
                                </x-logout-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</nav>

<!-- Alpine.js se carga en el layout principal -->
