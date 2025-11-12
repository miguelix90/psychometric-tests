<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Participante</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    Panel de Evaluaciones
                </h1>
                <form method="POST" action="{{ route('participant.logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                        Salir →
                    </button>
                </form>
            </div>
        </div>

        <!-- Content -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Mensaje de bienvenida -->
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Información del participante -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-2">Tu Información</h2>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Edad</p>
                                <p class="font-semibold text-gray-900">{{ $participant->getFormattedAge() }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Sexo</p>
                                <p class="font-semibold text-gray-900">{{ $participant->sex->label() }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Institución</p>
                                <p class="font-semibold text-gray-900">{{ $participant->institution->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sesiones disponibles -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Mis Evaluaciones</h2>

                        @if($testSessions->count() > 0)
                            <div class="space-y-4">
                                @foreach($testSessions as $session)
                                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                                        <div class="flex items-start justify-between mb-4">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    {{ $session->battery->name }}
                                                </h3>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    {{ $session->battery->description }}
                                                </p>
                                            </div>
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                {{ $session->status->color() === 'gray' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $session->status->color() === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $session->status->color() === 'green' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $session->status->color() === 'red' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ $session->status->label() }}
                                            </span>
                                        </div>

                                        <!-- Información adicional -->
                                        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                                            <div>
                                                <p class="text-gray-500">Tipo</p>
                                                <p class="font-semibold text-gray-900">{{ $session->battery->type->label() }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500">Asignada</p>
                                                <p class="font-semibold text-gray-900">{{ $session->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                            @if($session->started_at)
                                                <div>
                                                    <p class="text-gray-500">Iniciada</p>
                                                    <p class="font-semibold text-gray-900">{{ $session->started_at->format('d/m/Y H:i') }}</p>
                                                </div>
                                            @endif
                                            @if($session->completed_at)
                                                <div>
                                                    <p class="text-gray-500">Completada</p>
                                                    <p class="font-semibold text-gray-900">{{ $session->completed_at->format('d/m/Y H:i') }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Acciones -->
                                        <div class="flex items-center gap-3">
                                            @if($session->isPending())
                                                <a href="{{ route('test.session.show', $session) }}"
                                                   class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                                                    Iniciar Evaluación
                                                </a>
                                                <p class="text-sm text-gray-500">
                                                    {{ $session->battery->tasks->count() }} tareas por completar
                                                </p>
                                            @elseif($session->isInProgress())
                                                <a href="{{ route('test.session.show', $session) }}"
                                                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                                                    Continuar Evaluación
                                                </a>
                                                <p class="text-sm text-gray-500">
                                                    Progreso en curso...
                                                </p>
                                            @elseif($session->isCompleted())
                                                <span class="text-green-600 font-semibold flex items-center">
                                                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Evaluación Completada
                                                </span>
                                            @elseif($session->isAbandoned())
                                                <span class="text-red-600 font-semibold flex items-center">
                                                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Sesión Abandonada
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay evaluaciones asignadas</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Contacta con tu profesor para que te asigne una evaluación.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
