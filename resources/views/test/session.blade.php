@extends('layouts.test')

@section('title', 'Tu Evaluación')

@section('progress-bar')
    <div class="px-6 py-4">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-semibold">{{ $testSession->battery->name }}</h1>
            <span class="text-sm text-gray-600">
                {{ $completedTasks }} de {{ $totalTasks }} tareas completadas
            </span>
        </div>
        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                 style="width: {{ $progress }}%"></div>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">

            @if($testSession->isPending())
                <!-- Sesión pendiente - Mostrar información e iniciar -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold mb-4">Bienvenido a tu Evaluación</h2>
                    <p class="text-lg text-gray-700 mb-2">{{ $testSession->battery->name }}</p>

                </div>

                <!-- Lista de tareas -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4">Tareas a realizar:</h3>
                    <div class="space-y-3">
                        @foreach($testSession->testSessionTasks as $task)
                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-blue-600 font-semibold">{{ $task->order }}</span>
                                </div>
                                <div class="flex-grow">
                                    <h4 class="font-semibold">{{ $task->task->name }}</h4>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Instrucciones importantes -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <h4 class="font-semibold text-blue-800 mb-2">Instrucciones importantes:</h4>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Lee atentamente cada ítem antes de responder</li>
                        <li>No podrás retroceder una vez respondas</li>
                        <li>Tómate el tiempo necesario, no hay prisa</li>
                        <li>Responde con sinceridad</li>
                    </ul>
                </div>

                <!-- Botón iniciar -->
                <form method="POST" action="{{ route('test.session.start', $testSession) }}">
                    @csrf
                    <button type="submit"
                            class="w-full py-4 bg-blue-600 text-white text-lg font-semibold
                                   rounded-lg hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl">
                        Comenzar Evaluación
                    </button>
                </form>

            @elseif($testSession->isInProgress())
                <!-- Sesión en progreso - Continuar -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold mb-4">Evaluación en Progreso</h2>
                    <p class="text-lg text-gray-700">{{ $testSession->battery->name }}</p>
                </div>

                <!-- Progreso de tareas -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4">Tu progreso:</h3>
                    <div class="space-y-3">
                        @foreach($testSession->testSessionTasks as $task)
                            <div class="flex items-center p-4 rounded-lg
                                {{ $task->isCompleted() ? 'bg-green-50' : ($task->isInProgress() ? 'bg-blue-50' : 'bg-gray-50') }}">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-4
                                    {{ $task->isCompleted() ? 'bg-green-500' : ($task->isInProgress() ? 'bg-blue-500' : 'bg-gray-300') }}">
                                    @if($task->isCompleted())
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        <span class="text-white font-semibold text-sm">{{ $task->order }}</span>
                                    @endif
                                </div>
                                <div class="flex-grow">
                                    <h4 class="font-semibold">{{ $task->task->name }}</h4>
                                    <p class="text-sm text-gray-600">
                                        @if($task->isCompleted())
                                            <span class="text-green-600 font-medium">✓ Completada</span>
                                        @elseif($task->isInProgress())
                                            <span class="text-blue-600 font-medium">→ En progreso</span>
                                        @else
                                            <span class="text-gray-500">Pendiente</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Botón continuar -->
                @php
                    $currentTask = $testSession->testSessionTasks->firstWhere('status', \App\Enums\TestSessionTaskStatus::IN_PROGRESS);
                    if (!$currentTask) {
                        $currentTask = $testSession->testSessionTasks->firstWhere('status', \App\Enums\TestSessionTaskStatus::NOT_STARTED);
                    }
                @endphp

                @if($currentTask)
                    <a href="{{ $currentTask->getExecutionUrl() }}"
                       class="block w-full py-4 bg-blue-600 text-white text-lg font-semibold text-center
                              rounded-lg hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl">
                        Continuar Evaluación
                    </a>
                @endif

            @elseif($testSession->isCompleted())
                <!-- Sesión completada -->
                <div class="text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-20 w-20 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold mb-4 text-green-600">¡Evaluación Completada!</h2>
                    <p class="text-lg text-gray-700 mb-6">
                        Has completado todas las tareas de la evaluación "{{ $testSession->battery->name }}".
                    </p>
                    <p class="text-gray-600">
                        Tus resultados serán evaluados por el profesional responsable.
                    </p>
                </div>

            @else
                <!-- Sesión abandonada u otro estado -->
                <div class="text-center">
                    <h2 class="text-2xl font-bold mb-4">Sesión {{ $testSession->status->label() }}</h2>
                    <p class="text-gray-600">Por favor, contacta con el responsable de la evaluación.</p>
                </div>
            @endif

        </div>
    </div>
@endsection
