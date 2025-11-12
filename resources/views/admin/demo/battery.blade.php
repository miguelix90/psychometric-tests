<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Demo de Batería: {{ $battery->name }}
            </h2>
            <form action="{{ route('admin.demo.exit') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    ❌ Salir del Demo
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Banner de modo demo -->
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                <div class="flex items-center">
                    <div class="py-1">
                        <svg class="fill-current h-6 w-6 text-yellow-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold">Modo Demo Activado</p>
                        <p class="text-sm">Las respuestas NO se guardarán en la base de datos. Este es solo un preview de la batería completa.</p>
                    </div>
                </div>
            </div>

            <!-- Información de la batería -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $battery->name }}</h3>

                    @if($battery->description)
                        <p class="text-gray-700 mb-6">{{ $battery->description }}</p>
                    @endif

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Tipo de Batería</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $battery->type->label() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Total de Tareas</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $battery->tasks->count() }}</p>
                        </div>
                    </div>

                    <h4 class="text-lg font-semibold mb-3">Tareas incluidas:</h4>
                    <ol class="list-decimal list-inside space-y-2 mb-6">
                        @foreach($battery->tasks as $task)
                            <li class="text-gray-700">
                                <span class="font-medium">{{ $task->name }}</span>
                                <span class="text-sm text-gray-500">({{ $task->activeItems()->count() }} ítems)</span>
                                <span class="ml-2 px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">{{ $task->type->label() }}</span>
                            </li>
                        @endforeach
                    </ol>

                    <div class="bg-gray-50 p-4 rounded border border-gray-200">
                        <p class="text-sm text-gray-600 mb-1">Total de ítems en esta batería:</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalItems }}</p>
                    </div>
                </div>
            </div>

            <!-- Instrucciones -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="text-lg font-semibold mb-3">📋 Instrucciones</h4>
                    <ul class="space-y-2 text-gray-700">
                        <li>✓ Completarás todas las tareas en el orden establecido</li>
                        <li>✓ Cada tarea tiene sus propias instrucciones</li>
                        <li>✓ No podrás retroceder a ítems anteriores</li>
                        <li>✓ Este es un modo demo: las respuestas NO se guardan</li>
                        <li>✓ Puedes salir en cualquier momento usando el botón "Salir del Demo"</li>
                    </ul>
                </div>
            </div>

            <!-- Botón para comenzar -->
            <div class="text-center">
                <form action="{{ route('admin.demo.battery.start.execution') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white text-lg font-bold rounded-lg shadow-lg transition-all">
                        🚀 Comenzar Demo de Batería
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
