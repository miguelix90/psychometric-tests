<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle de Tarea') }}: {{ $task->name }}
            </h2>
            <div class="flex space-x-2">
                {{-- Botón Ver Items - Detecta tipo de tarea --}}
                @if($task->isMatrix())
                    <a href="{{ route('admin.tasks.items.index', $task) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Ver Ítems
                    </a>
                @elseif($task->isSpatial())
                    <a href="{{ route('admin.tasks.items.spatial.index', $task) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        Ver Ítems
                    </a>
                @endif

                {{-- Botón Crear Item - Detecta tipo de tarea --}}
                @if($task->isMatrix())
                    <a href="{{ route('admin.tasks.items.create', $task) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Crear Ítem
                    </a>
                @elseif($task->isSpatial())
                    <a href="{{ route('admin.tasks.items.spatial.create', $task) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Crear Ítem
                    </a>
                @endif

                {{-- Botón Probar Demo (solo si hay items activos) --}}
                @if($activeItemsCount > 0)
                    <a href="{{ route('admin.demo.start', $task) }}"
                    class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Probar Demo
                    </a>
                @endif

                {{-- Botón Volver --}}
                <a href="{{ route('admin.tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Volver al Listado
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Información de la tarea -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Información General</h3>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nombre</p>
                            <p class="text-base text-gray-900">{{ $task->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Tipo</p>
                            <p class="text-base text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $task->type->label() }}
                                </span>
                            </p>
                        </div>

                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500">Descripción</p>
                            <p class="text-base text-gray-900">{{ $task->description }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Total de Ítems</p>
                            <p class="text-base text-gray-900">{{ $task->items->count() }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Ítems Activos</p>
                            <p class="text-base text-gray-900">{{ $activeItemsCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de ítems -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Ítems de esta Tarea</h3>

                    @if($task->items->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Código
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dificultad
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($task->items as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $item->code }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $item->difficulty }}
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
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                @if($task->isMatrix())
                                                    <a href="{{ route('admin.tasks.items.edit', [$task, $item]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Editar
                                                    </a>
                                                @elseif($task->isSpatial())
                                                    <a href="{{ route('admin.tasks.items.spatial.edit', [$task, $item]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        Editar
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">
                            Esta tarea no tiene ítems asignados.
                            @if($task->isMatrix())
                                <a href="{{ route('admin.tasks.items.create', $task) }}" class="text-blue-600 hover:text-blue-800">
                                    Crear el primero
                                </a>
                            @elseif($task->isSpatial())
                                <a href="{{ route('admin.tasks.items.spatial.create', $task) }}" class="text-blue-600 hover:text-blue-800">
                                    Crear el primero
                                </a>
                            @endif
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
