<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#10b981">
    <title>Login - Gepro Avícola</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Fondo con imagen opaca */
        .bg-image {
            background-image: url('{{ asset('images/fondo.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        
        /* Overlay oscuro sobre la imagen */
        .bg-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }
        
        /* Contenedor del formulario */
        .form-container {
            position: relative;
            z-index: 2;
        }
        
        /* Efecto glassmorphism para el formulario */
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        /* Estilos para inputs */
        input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            background-color: white;
            transition: all 0.3s ease;
        }
        
        input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        /* Botón con gradiente */
        .btn-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }
        
        /* Animación de entrada */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Logo con sombra */
        .logo-shadow {
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.3));
        }
        
        /* Input con icono */
        .input-with-icon {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #10b981;
            pointer-events: none;
        }
        
        .input-with-icon input {
            padding-left: 2.75rem;
        }
    </style>
</head>
<body class="bg-image">
    <div class="min-h-screen flex items-center justify-center px-4 py-8 sm:px-6 lg:px-8 form-container">
        <div class="glass-effect p-8 rounded-2xl w-full max-w-md animate-fade-in">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.jpg') }}" alt="Gepro Avícola" class="h-20 w-20 mx-auto mb-4 rounded-full logo-shadow object-cover">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Bienvenido</h2>
                <p class="text-gray-600">Inicia sesión en Gepro Avícola</p>
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
                    <label for="Email" class="block text-gray-700 font-semibold mb-2">Correo Electrónico</label>
                    <div class="input-with-icon">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <input type="email" name="Email" id="Email" required 
                               value="{{ old('Email') }}"
                               placeholder="correo@ejemplo.com">
                    </div>
                </div>

                <div>
                    <label for="Contrasena" class="block text-gray-700 font-semibold mb-2">Contraseña</label>
                    <div class="input-with-icon">
                        <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <input type="password" name="Contrasena" id="Contrasena" required 
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="submitBtn"
                            class="w-full flex items-center justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-base font-bold text-white btn-gradient focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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

            <div class="mt-6 text-center border-t border-gray-200 pt-6">
                <p class="text-sm text-gray-700">
                    ¿No tienes una cuenta? 
                    <a href="{{ route('register') }}" class="font-semibold text-green-600 hover:text-green-700 transition-colors">
                        Regístrate aquí
                    </a>
                </p>
                <a href="{{ route('welcome') }}" class="inline-block mt-4 text-sm text-gray-600 hover:text-gray-800 transition-colors flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al inicio
                </a>
            </div>
        </div>
    </div>
</body>
</html>