<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle de Batería: ') . $battery->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.batteries.edit', $battery) }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Editar
                </a>
                <a href="{{ route('admin.batteries.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Volver al Listado
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Información General -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Información General</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                            <p class="text-gray-900">{{ $battery->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                                @if($battery->type->value === 'screening') bg-blue-100 text-blue-800
                                @elseif($battery->type->value === 'complete') bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $battery->type->label() }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            @if($battery->is_active)
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Activa
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactiva
                                </span>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tiene Puntuación</label>
                            @if($battery->has_scoring)
                                <span class="text-green-600 font-semibold">✓ Sí</span>
                            @else
                                <span class="text-gray-400 font-semibold">✗ No</span>
                            @endif
                        </div>

                        @if($battery->description)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <p class="text-gray-900">{{ $battery->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tareas Asignadas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">
                        Tareas Asignadas ({{ $battery->tasks->count() }})
                    </h3>

                    @if($battery->tasks->count() > 0)
                        <div class="space-y-3">
                            @foreach($battery->tasks as $task)
                                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                    <!-- Número de orden -->
                                    <div class="flex-shrink-0 w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-lg">
                                        {{ $task->pivot->order }}
                                    </div>

                                    <!-- Información de la tarea -->
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900">{{ $task->name }}</h4>
                                        @if($task->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $task->description }}</p>
                                        @endif
                                    </div>

                                    <!-- Badge con número de ítems activos -->
                                    <div class="flex-shrink-0">
                                        <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-sm font-medium">
                                            {{ $task->activeItems->count() }} ítems activos
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Resumen -->
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded">
                            <h4 class="font-semibold text-blue-900 mb-2">Resumen de la Batería</h4>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li>• Total de tareas: <strong>{{ $battery->tasks->count() }}</strong></li>
                                <li>• Total de ítems activos: <strong>{{ $battery->tasks->sum(fn($task) => $task->activeItems->count()) }}</strong></li>
                                <li>• Tipo: <strong>{{ $battery->type->label() }}</strong></li>
                                <li>• {{ $battery->type->description() }}</li>
                            </ul>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">Esta batería no tiene tareas asignadas todavía.</p>
                            <a href="{{ route('admin.batteries.edit', $battery) }}"
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Asignar Tareas
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Metadatos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Metadatos</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <label class="block text-gray-600">Creada</label>
                            <p class="text-gray-900">{{ $battery->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-gray-600">Última actualización</label>
                            <p class="text-gray-900">{{ $battery->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
