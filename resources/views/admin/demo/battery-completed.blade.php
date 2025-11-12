<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Demo Completado: {{ $battery->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Mensaje de éxito -->
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-6 mb-6" role="alert">
                <div class="flex items-center">
                    <div class="py-1">
                        <svg class="fill-current h-8 w-8 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-lg">¡Demo de Batería Completado!</p>
                        <p class="text-sm">Has completado todas las tareas de la batería en modo demo.</p>
                    </div>
                </div>
            </div>

            <!-- Resumen de la batería -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Resumen del Demo</h3>

                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded text-center">
                            <p class="text-sm text-gray-600">Batería</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $battery->name }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded text-center">
                            <p class="text-sm text-gray-600">Tareas</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $battery->tasks->count() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded text-center">
                            <p class="text-sm text-gray-600">Ítems Respondidos</p>
                            <p class="text-lg font-semibold text-gray-900">{{ count($responses) }}</p>
                        </div>
                    </div>

                    @if(count($responses) > 0)
                        <!-- Simulación de resultados (solo en demo) -->
                        @php
                            $correct = collect($responses)->where('is_correct', true)->count();
                            $incorrect = collect($responses)->where('is_correct', false)->count();
                            $avgTime = collect($responses)->avg('response_time_ms');
                        @endphp

                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold mb-3">Estadísticas del Demo</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center p-4 bg-green-50 rounded">
                                    <p class="text-3xl font-bold text-green-600">{{ $correct }}</p>
                                    <p class="text-sm text-gray-600">Correctas</p>
                                </div>
                                <div class="text-center p-4 bg-red-50 rounded">
                                    <p class="text-3xl font-bold text-red-600">{{ $incorrect }}</p>
                                    <p class="text-sm text-gray-600">Incorrectas</p>
                                </div>
                                <div class="text-center p-4 bg-blue-50 rounded">
                                    <p class="text-3xl font-bold text-blue-600">{{ number_format($avgTime / 1000, 1) }}s</p>
                                    <p class="text-sm text-gray-600">Tiempo promedio</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 bg-yellow-50 p-4 rounded border border-yellow-200">
                            <p class="text-sm text-yellow-800">
                                <strong>Nota:</strong> Estas estadísticas son solo para el demo. En una evaluación real,
                                los resultados se calcularían según los baremos apropiados.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recordatorio -->
            <div class="bg-gray-100 p-4 rounded mb-6 text-center">
                <p class="text-gray-700">
                    <strong>Recordatorio:</strong> Este fue un modo demo. Ninguna respuesta fue guardada en la base de datos.
                </p>
            </div>

            <!-- Acciones -->
            <div class="flex justify-center space-x-4">
                <a href="{{ route('admin.batteries.show', $battery) }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg">
                    Ver Batería
                </a>
                <a href="{{ route('admin.batteries.index') }}" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg">
                    Volver al Listado
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
