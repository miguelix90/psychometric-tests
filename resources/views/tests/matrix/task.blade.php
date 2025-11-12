@extends('layouts.test')

@section('title', $testSessionTask->task->name)

@section('progress-bar')
    <div class="px-6 py-4">
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-lg font-semibold">{{ $testSessionTask->task->name }}</h1>
            <span class="text-sm text-gray-600">
                {{ $answeredItems }} de {{ $totalItems }} completados
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                 style="width: {{ $progress }}%"></div>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">

            @if($testSessionTask->isNotStarted())
                <!-- Instrucciones antes de iniciar -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">Instrucciones</h2>
                    <div class="prose prose-lg">
                        {!! nl2br(e($testSessionTask->task->instructions)) !!}
                    </div>
                </div>

                <!-- Información importante -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <strong>Importante:</strong> Una vez que inicies esta tarea,
                        no podrás retroceder a ítems anteriores. Asegúrate de leer
                        bien cada ítem antes de responder.
                    </p>
                </div>

                <!-- Información de ítems -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <p class="text-gray-700">
                        Esta tarea contiene <strong>{{ $totalItems }} ítems</strong>.
                    </p>
                </div>

                <!-- Botón iniciar -->
                <form method="POST" action="{{ route('test.matrix.task.start', $testSessionTask) }}">
                    @csrf
                    <button type="submit"
                            class="w-full py-4 bg-blue-600 text-white text-lg font-semibold
                                   rounded-lg hover:bg-blue-700 transition shadow-lg hover:shadow-xl">
                        Comenzar Tarea
                    </button>
                </form>

            @elseif($testSessionTask->isInProgress())
                <!-- Tarea en progreso -->
                <div class="text-center">
                    <p class="text-lg text-gray-700 mb-6">
                        Tarea en progreso... Redirigiendo al siguiente ítem.
                    </p>
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                </div>

            @else
                <!-- Tarea completada -->
                <div class="text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-4">Tarea Completada</h2>
                    <p class="text-gray-700">Has completado esta tarea exitosamente.</p>
                </div>
            @endif

        </div>
    </div>
@endsection
