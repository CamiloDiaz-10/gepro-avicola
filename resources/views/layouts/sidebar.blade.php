<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="bg-blue-900 text-white w-64 min-h-screen fixed left-0 top-0 transform transition-transform duration-200 ease-in-out md:translate-x-0 z-30">
    <!-- Logo -->
    <div class="p-6 border-b border-blue-800">
        <h1 class="text-2xl font-bold">Gepro Avícola</h1>
    </div>

    <!-- Navigation -->
    <nav class="p-4">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center p-3 text-white rounded hover:bg-blue-800 {{ request()->routeIs('dashboard') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-home mr-3"></i>
                    <span>Inicio</span>
                </a>
            </li>

            <li>
                <a href="{{ route('fincas.index') }}" class="flex items-center p-3 text-white rounded hover:bg-blue-800 {{ request()->routeIs('fincas.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-warehouse mr-3"></i>
                    <span>Fincas</span>
                </a>
            </li>

            <li>
                <a href="{{ route('birds.index') }}" class="flex items-center p-3 text-white rounded hover:bg-blue-800 {{ request()->routeIs('birds.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-feather mr-3"></i>
                    <span>Aves</span>
                </a>
            </li>

            @can('Propietario')
            <li>
                <a href="{{ route('fincas.index') }}" class="flex items-center p-3 text-white rounded hover:bg-blue-800 {{ request()->routeIs('fincas.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-warehouse mr-3"></i>
                    <span>Gestión de Fincas</span>
                </a>
            </li>

            <li>
                <a href="{{ route('birds.index') }}" class="flex items-center p-3 text-white rounded hover:bg-blue-800 {{ request()->routeIs('birds.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-feather mr-3"></i>
                    <span>Control de Aves</span>
                </a>
            </li>

            <li>
                <a href="{{ route('egg-production.index') }}" class="flex items-center p-3 text-white rounded hover:bg-blue-800 {{ request()->routeIs('egg-production.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-egg mr-3"></i>
                    <span>Producción de Huevos</span>
                </a>
            </li>

            <li>
                <a href="{{ route('reports.index') }}" class="flex items-center p-3 text-white rounded hover:bg-blue-800 {{ request()->routeIs('reports.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-chart-line mr-3"></i>
                    <span>Informes y Reportes</span>
                </a>
            </li>
            @endcan

            <li>
                <a href="{{ route('profile.index') }}" class="flex items-center p-3 text-white rounded hover:bg-blue-800 {{ request()->routeIs('profile.*') ? 'bg-blue-800' : '' }}">
                    <i class="fas fa-user-circle mr-3"></i>
                    <span>Mi Perfil</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Mobile Sidebar Overlay -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden">
</div>