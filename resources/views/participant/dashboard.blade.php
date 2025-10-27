<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Participante</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <!-- Barra superior -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-gray-800">
                        {{ $participant->institution->name }}
                    </h1>
                </div>
                <div class="flex items-center">
                    <form method="POST" action="{{ route('participant.logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="min-h-screen py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensaje de bienvenida -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">
                        ¡Bienvenido/a!
                    </h2>
                    <p class="text-gray-600">
                        Has accedido correctamente al sistema de evaluación.
                    </p>
                </div>
            </div>

            <!-- Información del participante -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Tu Información
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Código IUC (tu identificador único)</p>
                            <p class="text-sm font-mono bg-gray-50 p-2 rounded mt-1 break-all">
                                {{ $participant->iuc }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Edad</p>
                            <p class="text-base font-semibold text-gray-800 mt-1">
                                {{ $participant->getFormattedAge() }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Sexo</p>
                            <p class="text-base font-semibold text-gray-800 mt-1">
                                {{ $participant->sex->label() }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Institución</p>
                            <p class="text-base font-semibold text-gray-800 mt-1">
                                {{ $participant->institution->name }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Próximos pasos / Estado de evaluaciones -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Estado de tus Evaluaciones
                    </h3>

                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Próximamente:</strong> Aquí verás las evaluaciones asignadas y podrás comenzar a realizarlas.
                                </p>
                                <p class="text-sm text-blue-700 mt-2">
                                    Esta funcionalidad está en desarrollo. Tu profesor/evaluador te informará cuando puedas comenzar.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de privacidad -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="h-5 w-5 text-green-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Protección de Datos
                    </h3>
                    <p class="text-sm text-gray-600">
                        Tu privacidad es importante. Solo se almacena información anonimizada mediante códigos únicos.
                        No guardamos nombres, apellidos ni fechas de nacimiento en nuestro sistema.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500">
                Sistema de Evaluación Psicométrica Online
            </p>
        </div>
    </footer>
</body>
</html>
