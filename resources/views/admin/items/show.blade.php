<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle del Ítem') }}: {{ $item->code }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.items.edit', $item) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                <a href="{{ route('admin.items.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Volver al Listado
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Información básica -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Información Básica</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Código</p>
                                <p class="text-base text-gray-900">{{ $item->code }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Tarea</p>
                                <p class="text-base text-gray-900">{{ $item->task->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Dificultad</p>
                                <p class="text-base text-gray-900">{{ $item->difficulty }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Respuesta Correcta</p>
                                <p class="text-base text-gray-900">{{ $item->correct_answer }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Estado</p>
                                <p class="text-base">
                                    @if($item->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactivo
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Creado</p>
                                <p class="text-base text-gray-900">{{ $item->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Preview del contenido -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Preview del Ítem</h3>

                        @if(isset($item->content['matrix_image']))
                            <div class="mb-6">
                                <p class="text-sm font-medium text-gray-500 mb-2">Imagen de la Matriz</p>
                                <div class="border border-gray-300 rounded p-4 bg-gray-50">
                                    <img src="{{ asset('storage/' . $item->content['matrix_image']) }}" alt="Matriz" class="max-w-md mx-auto">
                                </div>
                            </div>
                        @endif

                        @if(isset($item->content['options']) && is_array($item->content['options']))
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-2">Opciones de Respuesta</p>
                                <div class="grid grid-cols-3 gap-4">
                                    @foreach($item->content['options'] as $key => $option)
                                        @if($option)
                                            <div class="border border-gray-300 rounded p-2 bg-gray-50">
                                                <p class="text-xs font-medium text-gray-600 mb-1">Opción {{ $key }}</p>
                                                <img src="{{ asset('storage/' . $option) }}" alt="Opción {{ $key }}" class="w-full">
                                                @if($item->correct_answer == $key)
                                                    <p class="text-xs text-green-600 font-semibold mt-1">✓ Correcta</p>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- JSON Raw -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Contenido JSON</h3>
                        <div class="bg-gray-100 p-4 rounded overflow-x-auto">
                            <pre class="text-sm">{{ json_encode($item->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex justify-between">
                        <form action="{{ route('admin.items.destroy', $item) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este ítem? Se eliminarán también todas sus imágenes.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Eliminar Ítem
                            </button>
                        </form>

                        <a href="{{ route('admin.items.edit', $item) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Editar Ítem
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
