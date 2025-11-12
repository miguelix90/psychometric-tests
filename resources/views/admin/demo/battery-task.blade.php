<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Demo: {{ $task->name }}
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
                        <p class="font-bold">Modo Demo - Batería: {{ $battery->name }}</p>
                        <p class="text-sm">Tarea {{ $currentTaskNumber }} de {{ $totalTasks }}</p>
                    </div>
                </div>
            </div>

            <!-- Información de la tarea -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $task->name }}</h3>
                        <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                            {{ $task->type->label() }}
                        </span>
                    </div>

                    @if($task->description)
                        <p class="text-gray-700 mb-4">{{ $task->description }}</p>
                    @endif

                    <div class="bg-gray-50 p-4 rounded border border-gray-200">
                        <p class="text-sm text-gray-600 mb-1">Ítems en esta tarea:</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalItems }}</p>
                    </div>
                </div>
            </div>

            <!-- Instrucciones generales -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="text-lg font-semibold mb-3">📋 Instrucciones</h4>

                    @if($task->instructions)
                        <div class="prose max-w-none mb-4">
                            {!! nl2br(e($task->instructions)) !!}
                        </div>
                    @endif

                    <ul class="space-y-2 text-gray-700">
                        <li>✓ Lee cuidadosamente cada ítem</li>
                        <li>✓ Selecciona la respuesta que consideres correcta</li>
                        <li>✓ No podrás volver a ítems anteriores</li>
                        <li>✓ Tómate el tiempo que necesites en cada ítem</li>
                    </ul>
                </div>
            </div>

            <!-- Botón para comenzar -->
            <div class="text-center">
                <form action="{{ route('admin.demo.battery.task.start') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white text-lg font-bold rounded-lg shadow-lg transition-all">
                        🚀 Comenzar Tarea
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
