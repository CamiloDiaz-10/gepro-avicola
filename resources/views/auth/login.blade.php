<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#10b981">
    <title>Login - Gepro Avícola</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="bg-white p-6 sm:p-8 rounded-lg shadow-xl w-full max-w-md">
            <!-- Logo/Header -->
            <div class="text-center mb-6">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-green-500 to-green-700 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg width="1024px" height="1024px" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
   <circle cx="512" cy="512" r="512" style="fill:#1CA67B"/>
   <path d="m458.15 617.7 18.8-107.3a56.94 56.94 0 0 1 35.2-101.9V289.4h-145.2a56.33 56.33 0 0 0-56.3 56.3v275.8a33.94 33.94 0 0 0 3.4 15c12.2 24.6 60.2 103.7 197.9 164.5V622.1a313.29 313.29 0 0 1-53.8-4.4zM656.85 289h-144.9v119.1a56.86 56.86 0 0 1 35.7 101.4l18.8 107.8A320.58 320.58 0 0 1 512 622v178.6c137.5-60.5 185.7-139.9 197.9-164.5a33.94 33.94 0 0 0 3.4-15V345.5a56 56 0 0 0-16.4-40 56.76 56.76 0 0 0-40.05-16.5z" style="fill:#fff"/>
</svg>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">Iniciar Sesión</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-2">Gepro Avícola</p>
            </div>

            @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 sm:p-4 mb-4 rounded">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm">{{ $errors->first() }}</p>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5" id="loginForm">
                @csrf

                <div>
                    <label for="Email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                    <input type="email" name="Email" id="Email" required 
                           class="block w-full px-3 py-2 sm:py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition-colors"
                           value="{{ old('Email') }}"
                           placeholder="correo@ejemplo.com">
                </div>

                <div>
                    <label for="Contrasena" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" name="Contrasena" id="Contrasena" required 
                           class="block w-full px-3 py-2 sm:py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition-colors"
                           placeholder="••••••••">
                </div>

                <div>
                    <button type="submit" id="submitBtn"
                            class="w-full flex items-center justify-center py-2.5 sm:py-3 px-4 border border-transparent rounded-lg shadow-md text-sm sm:text-base font-semibold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <svg class="w-5 h-5 mr-2" id="loginIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span id="btnText">Iniciar Sesión</span>
                    </button>
                </div>
            </form>

            <script>
                document.getElementById('loginForm').addEventListener('submit', function(e) {
                    const btn = document.getElementById('submitBtn');
                    const btnText = document.getElementById('btnText');
                    const icon = document.getElementById('loginIcon');
                    
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btnText.textContent = 'Iniciando sesión...';
                    icon.classList.add('animate-spin');
                    
                    console.log('Formulario enviado');
                    console.log('Email:', document.getElementById('Email').value);
                    console.log('Action:', this.action);
                });
            </script>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    ¿No tienes una cuenta? 
                    <a href="{{ route('register') }}" class="font-semibold text-green-600 hover:text-green-700 transition-colors">
                        Regístrate aquí
                    </a>
                </p>
                <a href="{{ route('welcome') }}" class="inline-block mt-4 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    ← Volver al inicio
                </a>
            </div>
        </div>
    </div>
</body>
</html>