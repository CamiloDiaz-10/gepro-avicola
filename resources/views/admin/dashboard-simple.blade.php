@extends('layouts.app-with-sidebar')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Dashboard Administrativo</h1>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Bienvenido, {{ auth()->user()->Nombre }} {{ auth()->user()->Apellido }}</h2>
            <p class="text-gray-600">Email: {{ auth()->user()->Email }}</p>
            <p class="text-gray-600">Rol: {{ auth()->user()->role->NombreRol ?? 'Sin rol' }}</p>
            
            <div class="mt-6">
                <p class="text-green-600 font-semibold">✓ El sistema de autenticación está funcionando correctamente</p>
                <p class="text-gray-500 mt-2">La sesión se cerrará automáticamente cuando cierres el navegador.</p>
            </div>
        </div>
    </div>
</div>
@endsection
