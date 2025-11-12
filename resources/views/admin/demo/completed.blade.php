<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🎭 Demo Completado
            </h2>
            <form action="{{ route('admin.demo.exit') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Volver a Ítems
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensaje de Éxito --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-8 text-center">
                    <div class="inline-block p-4 bg-green-100 rounded-full mb-4">
                        <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        ¡Demo Completado!
                    </h1>
                    <p class="text-gray-600 text-lg">
                        Has completado la vista previa de la tarea <strong>{{ $task->name }}</strong>
                    </p>
                </div>
            </div>

            {{-- Información del Demo --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">📊 Resumen del Demo</h2>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-600 font-semibold">Tarea</p>
                            <p class="text-lg font-bold text-blue-900">{{ $task->name }}</p>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <p class="text-sm text-green-600 font-semibold">Respuestas Simuladas</p>
                            <p class="text-lg font-bold text-green-900">{{ count($responses) }}</p>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Recordatorio:</strong> Esta fue una vista previa. Ninguna respuesta se ha guardado en la base de datos.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex justify-center space-x-4">
                <form action="{{ route('admin.demo.reset') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                        🔄 Repetir Demo
                    </button>
                </form>

                <form action="{{ route('admin.demo.exit') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg">
                        ← Volver a Ítems
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
