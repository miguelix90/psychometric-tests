<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin Usos Disponibles - {{ $institution->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <!-- Ícono de advertencia -->
            <div class="flex justify-center mb-6">
                <div class="rounded-full bg-yellow-100 p-3">
                    <svg class="h-12 w-12 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>

            <!-- Título -->
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">
                    Sin Usos Disponibles
                </h1>
                <p class="text-gray-600">
                    {{ $institution->name }}
                </p>
            </div>

            <!-- Mensaje -->
            <div class="mb-6 text-center">
                <p class="text-gray-700">
                    Lo sentimos, esta institución ha agotado sus usos disponibles para realizar evaluaciones.
                </p>
            </div>

            <!-- Información adicional -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>¿Qué hacer?</strong><br>
                            Por favor, contacta con el responsable de tu institución o con tu profesor/evaluador para informarles de esta situación.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Detalles técnicos (solo para información) -->
            <div class="text-center text-xs text-gray-500 mb-4">
                <p>Usos disponibles: <strong>{{ $institution->available_uses }}</strong></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                Sistema de Evaluación Psicométrica Online
            </p>
        </div>
    </div>
</body>
</html>
