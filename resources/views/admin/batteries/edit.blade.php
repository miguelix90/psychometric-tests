<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Batería: ') . $battery->name }}
            </h2>
            <a href="{{ route('admin.batteries.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver al Listado
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.batteries.update', $battery) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Columna Izquierda: Datos de la Batería -->
                            <div>
                                <h3 class="text-lg font-semibold mb-4">Datos de la Batería</h3>

                                <!-- Nombre -->
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre de la Batería *
                                    </label>
                                    <input type="text"
                                           name="name"
                                           id="name"
                                           value="{{ old('name', $battery->name) }}"
                                           required
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                                    @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Descripción -->
                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Descripción
                                    </label>
                                    <textarea name="description"
                                              id="description"
                                              rows="4"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror">{{ old('description', $battery->description) }}</textarea>
                                    @error('description')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Tipo -->
                                <div class="mb-4">
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tipo de Batería *
                                    </label>
                                    <select name="type"
                                            id="type"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('type') border-red-500 @enderror">
                                        @foreach($types as $type)
                                            <option value="{{ $type->value }}" {{ old('type', $battery->type->value) == $type->value ? 'selected' : '' }}>
                                                {{ $type->label() }} - {{ $type->description() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Estado Activo -->
                                <div class="mb-4">
                                    <label class="flex items-center">
                                        <input type="checkbox"
                                               name="is_active"
                                               value="1"
                                               {{ old('is_active', $battery->is_active) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Batería activa</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Columna Derecha: Gestión de Tareas -->
                            <div>
                                <h3 class="text-lg font-semibold mb-4">Tareas de la Batería</h3>

                                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded">
                                    <p class="text-sm text-blue-800">
                                        Selecciona las tareas que formarán parte de esta batería y define su orden de presentación.
                                    </p>
                                </div>

                                <!-- Lista de tareas disponibles -->
                                <div class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded p-4">
                                    @foreach($availableTasks as $task)
                                        @php
                                            $isAssigned = $battery->tasks->contains($task->id);
                                            $currentOrder = $isAssigned ? $battery->tasks->find($task->id)->pivot->order : 0;
                                        @endphp
                                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded hover:bg-gray-100">
                                            <!-- Checkbox -->
                                            <input type="checkbox"
                                                   name="tasks[]"
                                                   value="{{ $task->id }}"
                                                   id="task_{{ $task->id }}"
                                                   {{ $isAssigned ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                                            <!-- Nombre de la tarea -->
                                            <label for="task_{{ $task->id }}" class="flex-1 text-sm font-medium text-gray-700 cursor-pointer">
                                                {{ $task->name }}
                                            </label>

                                            <!-- Campo de orden -->
                                            <div class="flex items-center gap-2">
                                                <label for="order_{{ $task->id }}" class="text-xs text-gray-600">Orden:</label>
                                                <input type="number"
                                                       name="task_orders[{{ $task->id }}]"
                                                       id="order_{{ $task->id }}"
                                                       value="{{ old("task_orders.{$task->id}", $currentOrder) }}"
                                                       min="0"
                                                       class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            </div>

                                            <!-- Botones de orden -->
                                            <div class="flex flex-col gap-1">
                                                <button type="button"
                                                        onclick="changeOrder({{ $task->id }}, -1)"
                                                        class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">
                                                    ↑
                                                </button>
                                                <button type="button"
                                                        onclick="changeOrder({{ $task->id }}, 1)"
                                                        class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">
                                                    ↓
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @error('tasks')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end gap-4 mt-6 pt-6 border-t">
                            <a href="{{ route('admin.batteries.index') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Actualizar Batería
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para manejar el orden de las tareas -->
    <script>
        function changeOrder(taskId, direction) {
            const input = document.getElementById('order_' + taskId);
            let currentValue = parseInt(input.value) || 0;
            let newValue = currentValue + direction;

            // No permitir valores negativos
            if (newValue < 0) newValue = 0;

            input.value = newValue;
        }
    </script>
</x-app-layout>
