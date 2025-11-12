<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código No Disponible</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">

            <!-- Ícono de error -->
            <div class="flex justify-center mb-6">
                <div class="rounded-full bg-red-100 p-3">
                    <svg class="h-12 w-12 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>

            <!-- Código -->
            <div class="mb-6 text-center">
                <div class="inline-block bg-gray-100 rounded-lg px-4 py-2 mb-4">
                    <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Código de Batería</p>
                    <p class="text-2xl font-mono font-bold text-gray-700">{{ $code }}</p>
                </div>
            </div>

            <!-- Título -->
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">
                    Código No Disponible
                </h1>
                <p class="text-gray-600">
                    Este código no puede ser utilizado en este momento.
                </p>
            </div>

            <!-- Razón del error -->
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Motivo
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ $reason }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles del código -->
            @if($batteryCode)
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Detalles del Código</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Batería:</span>
                            <span class="font-semibold text-gray-900">{{ $batteryCode->battery->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Estado:</span>
                            @if ($batteryCode->isExpired())
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Expirado
                                </span>
                            @elseif (!$batteryCode->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactivo
                                </span>
                            @elseif (!$batteryCode->hasUsesAvailable())
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Sin usos disponibles
                                </span>
                            @endif
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Usos:</span>
                            <span class="font-semibold text-gray-900">{{ $batteryCode->current_uses }} / {{ $batteryCode->max_uses }}</span>
                        </div>
                        @if($batteryCode->isExpired())
                            <div class="flex justify-between">
                                <span class="text-gray-600">Expiró:</span>
                                <span class="font-semibold text-gray-900">{{ $batteryCode->expires_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @else
                            <div class="flex justify-between">
                                <span class="text-gray-600">Expira:</span>
                                <span class="font-semibold text-gray-900">{{ $batteryCode->expires_at->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Instrucciones -->
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            ¿Qué hacer ahora?
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Verifica que el código sea correcto</li>
                                <li>Contacta con tu profesor o evaluador</li>
                                <li>Solicita un nuevo código si este ha expirado</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón para intentar otro código -->
            <div class="text-center">
                <a href="/"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Volver al Inicio
                </a>
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
