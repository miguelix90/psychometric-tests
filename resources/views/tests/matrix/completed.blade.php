@extends('layouts.test')

@section('title', 'Evaluación Completada')

@section('progress-bar')
    <div class="px-6 py-4">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-semibold">{{ $testSession->battery->name }}</h1>
            <span class="text-sm text-green-600 font-semibold">
                ✓ Completada
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">

            <!-- Icono de éxito -->
            <div class="mb-6">
                <svg class="mx-auto h-24 w-24 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <!-- Título -->
            <h1 class="text-3xl font-bold text-green-600 mb-4">
                ¡Evaluación Completada!
            </h1>

            <!-- Mensaje principal -->
            <p class="text-xl text-gray-700 mb-6">
                Has completado exitosamente la evaluación "{{ $testSession->battery->name }}".
            </p>

            <!-- Información adicional -->
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-8 text-left">
                <h3 class="font-semibold text-green-800 mb-2">¿Qué sigue ahora?</h3>
                <ul class="text-sm text-green-800 space-y-1">
                    <li>✓ Tus respuestas han sido guardadas correctamente</li>
                    <li>✓ El profesional responsable evaluará tus resultados</li>
                    <li>✓ Serás contactado cuando los resultados estén disponibles</li>
                </ul>
            </div>

            <!-- Información de la sesión -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                <h3 class="font-semibold text-gray-700 mb-3">Resumen de tu evaluación:</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Batería:</p>
                        <p class="font-semibold">{{ $testSession->battery->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Tareas completadas:</p>
                        <p class="font-semibold">{{ $testSession->testSessionTasks->count() }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Fecha de inicio:</p>
                        <p class="font-semibold">{{ $testSession->started_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Fecha de finalización:</p>
                        <p class="font-semibold">{{ $testSession->completed_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Mensaje de agradecimiento -->
            <div class="mb-6">
                <p class="text-gray-700 text-lg">
                    ¡Gracias por tu participación!
                </p>
                <p class="text-gray-600 text-sm mt-2">
                    Puedes cerrar esta ventana de forma segura.
                </p>
            </div>

            <!-- Botón para cerrar sesión (opcional) -->
            <form method="POST" action="{{ route('participant.logout') }}" class="mt-8">
                @csrf
                <button type="submit"
                        class="px-8 py-3 bg-gray-600 text-white font-semibold rounded-lg
                               hover:bg-gray-700 transition">
                    Cerrar Sesión
                </button>
            </form>

        </div>
    </div>
@endsection
