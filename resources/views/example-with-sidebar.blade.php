@extends('layouts.app-with-sidebar')

@section('title', 'Ejemplo con Sidebar - Gepro Avícola')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Ejemplo de Sincronización</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Estado del Sidebar -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold text-blue-900 mb-2">Estado del Sidebar</h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Sidebar Móvil Abierto:</span>
                                <span x-text="sidebarOpen ? 'Sí' : 'No'" class="font-mono"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Sidebar Desktop Colapsado:</span>
                                <span x-text="sidebarCollapsed ? 'Sí' : 'No'" class="font-mono"></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Ancho de Ventana:</span>
                                <span x-text="window.innerWidth + 'px'" class="font-mono"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Controles de Prueba -->
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold text-green-900 mb-2">Controles de Prueba</h2>
                        <div class="space-y-3">
                            <button @click="sidebarOpen = !sidebarOpen" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                Toggle Sidebar Móvil
                            </button>
                            <button @click="sidebarCollapsed = !sidebarCollapsed" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                Toggle Sidebar Desktop
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Información -->
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                ¿Cómo funciona la sincronización?
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>El navbar y sidebar comparten el mismo estado de Alpine.js</li>
                                    <li>El botón hamburguesa del navbar controla el sidebar</li>
                                    <li>En móvil: el botón abre/cierra el sidebar deslizante</li>
                                    <li>En desktop: el botón colapsa/expande el sidebar</li>
                                    <li>Los tooltips aparecen cuando el sidebar está colapsado</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contenido de ejemplo -->
                <div class="mt-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Contenido de Ejemplo</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Tarjeta 1</h3>
                            <p class="text-gray-600">Este contenido se adapta automáticamente al ancho del sidebar.</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Tarjeta 2</h3>
                            <p class="text-gray-600">El layout es completamente responsive y funciona en todos los dispositivos.</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Tarjeta 3</h3>
                            <p class="text-gray-600">La sincronización entre navbar y sidebar es perfecta.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
