<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin conexión - Gepro Avícola</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="max-w-md w-full bg-white shadow rounded-lg p-6 text-center">
        <div class="text-sky-500 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v4a1 1 0 00.293.707l2 2a1 1 0 101.414-1.414L11 10.586V7z" clip-rule="evenodd" />
            </svg>
        </div>
        <h1 class="text-xl font-semibold text-gray-800">Estás sin conexión</h1>
        <p class="text-gray-600 mt-2">No pudimos cargar la página. Revisa tu conexión a Internet.
        </p>
        <div class="mt-4 space-x-2">
            <button onclick="location.reload()" class="px-4 py-2 bg-sky-600 text-white rounded hover:bg-sky-700">Reintentar</button>
            <a href="/" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Ir al inicio</a>
        </div>
    </div>
</body>
</html>
