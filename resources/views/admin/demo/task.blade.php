<x-app-layout>
    {{-- Banner Demo Fijo --}}
    <div class="fixed top-0 left-0 right-0 z-50 bg-yellow-500 text-black px-4 py-2 text-center font-bold shadow-lg">
        ⚠️ MODO DEMO - Las respuestas NO se guardan
        <form action="{{ route('admin.demo.exit') }}" method="POST" class="inline ml-4">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                Salir del Demo
            </button>
        </form>
    </div>

    <div class="py-12 mt-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-8">

                    {{-- Título de la Tarea --}}
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            {{ $task->name }}
                        </h1>
                        <p class="text-gray-600">
                            {{ $task->description }}
                        </p>
                        <div class="mt-4 inline-block bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-semibold">
                            {{ $totalItems }} ítems en esta tarea
                        </div>
                    </div>

                    {{-- Instrucciones --}}
                    @if($task->instructions)
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                            <h2 class="text-xl font-semibold mb-4 text-gray-800">📋 Instrucciones</h2>
                            <div class="prose prose-sm max-w-none text-gray-700">
                                {!! nl2br(e($task->instructions)) !!}
                            </div>
                        </div>
                    @endif

                    {{-- Información adicional --}}
                    <div class="mb-8 p-6 bg-blue-50 rounded-lg border border-blue-200">
                        <h2 class="text-lg font-semibold mb-3 text-blue-900">ℹ️ Información importante</h2>
                        <ul class="list-disc list-inside space-y-2 text-blue-800">
                            <li>Esta es una vista previa en modo demo</li>
                            <li>Tus respuestas no se guardarán</li>
                            <li>Puedes cerrar y volver cuando quieras</li>
                            <li>No hay límite de tiempo</li>
                        </ul>
                    </div>

                    {{-- Botón Comenzar --}}
                    <div class="text-center">
                        <form action="{{ route('admin.demo.task.start') }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-12 rounded-lg text-lg shadow-lg transform transition hover:scale-105">
                                ▶️ Comenzar Tarea
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
