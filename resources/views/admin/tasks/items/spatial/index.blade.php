<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ítems de {{ $task->name }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Tipo: <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $task->type->label() }}
                    </span>
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.tasks.items.spatial.create', $task) }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Crear Nuevo Ítem
                </a>
                <a href="{{ route('admin.tasks.show', $task) }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Volver a la Tarea
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Tabla de ítems -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Código
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pregunta
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dificultad
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Respuesta Correcta
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Opciones
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $item->code }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ Str::limit($item->content['question_text'] ?? '', 50) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->difficulty }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $item->correct_answer }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ isset($item->content['options']) ? count($item->content['options']) : 0 }} opciones
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item->is_active)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Activo
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.tasks.items.spatial.edit', [$task, $item]) }}"
                                                   class="text-indigo-600 hover:text-indigo-900">
                                                    Editar
                                                </a>
                                                <form action="{{ route('admin.tasks.items.spatial.destroy', [$task, $item]) }}"
                                                      method="POST"
                                                      class="inline"
                                                      onsubmit="return confirm('¿Estás seguro de eliminar este ítem? Esta acción no se puede deshacer.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            No hay ítems para esta tarea.
                                            <a href="{{ route('admin.tasks.items.spatial.create', $task) }}" class="text-blue-600 hover:text-blue-800">
                                                Crear el primero
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($items->hasPages())
                        <div class="mt-4">
                            {{ $items->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
