@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Icono de advertencia -->
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                <svg class="h-12 w-12 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                Sin Acceso a Fincas
            </h2>
            
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                No tienes fincas asignadas en el sistema
            </p>
        </div>

        <!-- Mensaje principal -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="space-y-4">
                <p class="text-gray-700 dark:text-gray-300">
                    Para acceder al sistema necesitas estar asignado a al menos una finca. 
                    Actualmente no tienes ninguna finca vinculada a tu cuenta.
                </p>

                <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                <strong>¿Qué puedes hacer?</strong>
                            </p>
                            <p class="mt-1 text-sm text-blue-600 dark:text-blue-400">
                                Contacta al administrador del sistema para que te asigne a una o más fincas.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Información del usuario -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Información de tu cuenta:
                    </h3>
                    <dl class="space-y-1">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Nombre:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ auth()->user()->Nombre }} {{ auth()->user()->Apellido }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Email:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ auth()->user()->Email }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Rol:</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ auth()->user()->role->NombreRol ?? 'Sin rol' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex flex-col space-y-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    Cerrar Sesión
                </button>
            </form>

            <a href="{{ route('dashboard') }}" class="w-full flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                Volver al Dashboard
            </a>
        </div>

        <!-- Nota al pie -->
        <p class="text-center text-xs text-gray-500 dark:text-gray-400">
            Si crees que esto es un error, contacta al soporte técnico.
        </p>
    </div>
</div>
@endsection
