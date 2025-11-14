@extends('layouts.test')

@section('title', $testSessionTask->task->name)

@section('progress-bar')
    <div class="px-6 py-4">
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-lg font-semibold">{{ $testSessionTask->task->name }}</h1>
            <span class="text-sm text-gray-600">
                {{ $totalItems }} ítems
            </span>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto space-y-8">

        <!-- Instrucciones de la tarea -->
        <div class="bg-white rounded-lg shadow-xl p-8 lg:p-12">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">
                    {{ $testSessionTask->task->name }}
                </h2>
                <p class="text-gray-600">
                    {{ $testSessionTask->task->description }}
                </p>
            </div>

            @if($testSessionTask->task->instructions)
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">📋 Instrucciones:</h3>
                    <div class="text-gray-700 whitespace-pre-line">
                        {{ $testSessionTask->task->instructions }}
                    </div>
                </div>
            @endif

            <div class="space-y-4 mb-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-600 font-semibold">
                            1
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-700">
                            Lee cuidadosamente cada pregunta y observa la figura presentada
                        </p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-600 font-semibold">
                            2
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-700">
                            Selecciona la opción que consideres correcta entre las alternativas presentadas
                        </p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-600 font-semibold">
                            3
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-700">
                            No podrás volver a los ítems anteriores una vez que avances
                        </p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-600 font-semibold">
                            4
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-700">
                            Tómate el tiempo que necesites para responder cada ítem
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <p class="text-3xl font-bold text-blue-600">{{ $totalItems }}</p>
                        <p class="text-sm text-gray-600 mt-1">Total de ítems</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-green-600">{{ $answeredCount }}</p>
                        <p class="text-sm text-gray-600 mt-1">Completados</p>
                    </div>
                </div>
            </div>

            <!-- Botón para comenzar -->
            <div class="text-center">
                @if($answeredCount > 0 && $answeredCount < $totalItems)
                    <form action="{{ route('test.session.start', $testSessionTask->testSession) }}" method="POST">
                        @csrf
                        <input type="hidden" name="task_id" value="{{ $testSessionTask->task_id }}">
                        <button type="submit"
                                class="px-12 py-4 bg-orange-600 text-white text-lg font-semibold rounded-lg
                                       hover:bg-orange-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            Continuar Tarea
                        </button>
                    </form>
                @else
                    <form action="{{ route('test.session.start', $testSessionTask->testSession) }}" method="POST">
                        @csrf
                        <input type="hidden" name="task_id" value="{{ $testSessionTask->task_id }}">
                        <button type="submit"
                                class="px-12 py-4 bg-blue-600 text-white text-lg font-semibold rounded-lg
                                       hover:bg-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            Comenzar Tarea
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>
@endsection
