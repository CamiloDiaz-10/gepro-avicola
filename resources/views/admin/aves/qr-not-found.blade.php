@extends('layouts.app-with-sidebar')

@section('title', 'QR No Encontrado')

@section('content')
<div class="p-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white shadow rounded-lg p-8 text-center space-y-6">
            <div class="flex justify-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Ave No Encontrada</h1>
                <p class="text-gray-600">El código QR escaneado no corresponde a ningún ave registrada en el sistema.</p>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4 text-left">
                <div class="text-sm text-gray-700">
                    <div class="font-semibold mb-1">Token escaneado:</div>
                    <div class="font-mono text-xs break-all bg-white p-2 rounded border">{{ $token }}</div>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="text-sm text-gray-600">
                    <p class="font-semibold mb-2">Posibles causas:</p>
                    <ul class="text-left space-y-1 ml-6 list-disc">
                        <li>El ave fue eliminada del sistema</li>
                        <li>El QR fue generado antes de ejecutar las migraciones</li>
                        <li>El token no se generó correctamente al crear el ave</li>
                        <li>Hay un problema con la base de datos</li>
                    </ul>
                </div>
            </div>
            
            @auth
                @if(optional(auth()->user()->role)->NombreRol === 'Administrador')
                    <div class="pt-4 border-t">
                        <p class="text-sm text-gray-700 mb-3">
                            <strong>Solución para Administradores:</strong> Ejecuta el siguiente comando para generar tokens QR a todas las aves:
                        </p>
                        <div class="bg-gray-900 text-green-400 p-3 rounded font-mono text-sm">
                            php artisan birds:generate-qr-tokens
                        </div>
                    </div>
                @endif
            @endauth
            
            <div class="flex flex-wrap gap-3 justify-center pt-4">
                @auth
                    @php
                        $isAdmin = optional(auth()->user()->role)->NombreRol === 'Administrador';
                        $isOwner = optional(auth()->user()->role)->NombreRol === 'Propietario';
                    @endphp
                    @if($isAdmin)
                        <a href="{{ route('admin.aves.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Ver Todas las Aves
                        </a>
                        <a href="{{ route('admin.aves.scan') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Escanear Otro QR
                        </a>
                    @elseif($isOwner)
                        <a href="{{ route('owner.aves.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Ver Todas las Aves
                        </a>
                        <a href="{{ route('owner.aves.scan') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Escanear Otro QR
                        </a>
                    @else
                        <button onclick="window.history.back()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Volver
                        </button>
                    @endif
                @else
                    <button onclick="window.history.back()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Volver
                    </button>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
