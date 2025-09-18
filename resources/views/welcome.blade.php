@extends('layouts.app')

@section('title', 'Bienvenido - Gepro Avícola')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-50">
    <div class="container mx-auto px-4 py-16">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <div class="flex justify-center mb-8">
                <div class="relative group">
                    <!-- Logo container with enhanced styling -->
                    <div class="w-32 h-32 md:w-40 md:h-40 lg:w-48 lg:h-48 bg-gradient-to-br from-green-500 to-green-700 rounded-full flex items-center justify-center shadow-2xl transform transition-all duration-300 hover:scale-105 hover:shadow-3xl">
                        <!-- Inner white circle for better logo contrast -->
                        <div class="w-28 h-28 md:w-36 md:h-36 lg:w-44 lg:h-44 bg-white rounded-full flex items-center justify-center shadow-inner">
                            <img src="{{ asset('images/logo.jpg') }}" 
                                 alt="Gepro Avícola - Sistema de Gestión Avícola" 
                                 class="w-24 h-24 md:w-32 md:h-32 lg:w-40 lg:h-40 object-cover rounded-full transition-transform duration-300 group-hover:scale-110 border-2 border-green-100"
                                 loading="eager"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <!-- Fallback icon if image fails to load -->
                            <div class="hidden w-24 h-24 md:w-32 md:h-32 lg:w-40 lg:h-40 items-center justify-center text-green-600">
                                <svg class="w-16 h-16 md:w-20 md:h-20 lg:w-24 lg:h-24" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                </svg>
                            </div>
                        </div>
                        <!-- Decorative ring animation -->
                        <div class="absolute inset-0 rounded-full border-4 border-green-300 opacity-0 group-hover:opacity-100 animate-pulse transition-opacity duration-300"></div>
                    </div>
                    <!-- Subtle glow effect -->
                    <div class="absolute inset-0 rounded-full bg-green-400 opacity-20 blur-xl transform scale-110 group-hover:opacity-30 transition-opacity duration-300"></div>
                </div>
            </div>
            <h1 class="text-5xl font-bold text-gray-900 mb-6">
                Bienvenido a <span class="text-green-600">Gepro Avícola</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                Sistema integral de gestión avícola para el control y monitoreo de granjas, 
                producción de huevos, salud animal y optimización de recursos.
            </p>
            
            @auth
                <div class="space-y-4">
                    <p class="text-lg text-gray-700">
                        ¡Hola, <span class="font-semibold text-green-600">{{ auth()->user()->Nombre }}</span>! 
                        Bienvenido de vuelta.
                    </p>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"/>
                            </svg>
                            Ir al Dashboard
                        </a>
                        <x-logout-button 
                            class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150"
                            confirmMessage="¿Estás seguro de que deseas cerrar sesión?"
                        >
                            Cerrar Sesión
                        </x-logout-button>
                    </div>
                </div>
            @else
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Iniciar Sesión
                    </a>
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Registrarse
                    </a>
                </div>
            @endauth
        </div>

        <!-- Features Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Control de Producción</h3>
                <p class="text-gray-600">Monitoreo en tiempo real de la producción de huevos, calidad y rendimiento por lote.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Gestión de Salud</h3>
                <p class="text-gray-600">Seguimiento de la salud animal, tratamientos veterinarios y control de mortalidad.</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Reportes y Analytics</h3>
                <p class="text-gray-600">Informes detallados y análisis de datos para la toma de decisiones estratégicas.</p>
            </div>
        </div>

        <!-- Stats Section (only for authenticated users) -->
        @auth
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Resumen del Sistema</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2">{{ \App\Models\User::count() }}</div>
                    <div class="text-gray-600">Usuarios Registrados</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ \App\Models\Bird::count() }}</div>
                    <div class="text-gray-600">Aves en Sistema</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600 mb-2">{{ \App\Models\EggProduction::sum('CantidadHuevos') ?? 0 }}</div>
                    <div class="text-gray-600">Huevos Producidos</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-600 mb-2">{{ \Illuminate\Support\Facades\Schema::hasTable('fincas') ? \App\Models\Finca::count() : 0 }}</div>
                    <div class="text-gray-600">Fincas Registradas</div>
                </div>
            </div>
        </div>
        @endauth
    </div>
</div>
@endsection