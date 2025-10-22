<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Ítem') }}: {{ $item->code }}
            </h2>
            <a href="{{ route('admin.items.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver al Listado
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.items.update', $item) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Tarea -->
                        <div class="mb-4">
                            <label for="task_id" class="block text-sm font-medium text-gray-700">Tarea *</label>
                            <select name="task_id" id="task_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('task_id') border-red-500 @enderror">
                                <option value="">Selecciona una tarea</option>
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}" {{ old('task_id', $item->task_id) == $task->id ? 'selected' : '' }}>
                                        {{ $task->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('task_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Código -->
                        <div class="mb-4">
                            <label for="code" class="block text-sm font-medium text-gray-700">Código del Ítem *</label>
                            <input type="text" name="code" id="code" value="{{ old('code', $item->code) }}" required
                                   placeholder="Ej: MAT_001"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('code') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Formato recomendado: TAREA_NNN (Ej: MAT_001, NUM_015)</p>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dificultad -->
                        <div class="mb-4">
                            <label for="difficulty" class="block text-sm font-medium text-gray-700">Dificultad *</label>
                            <input type="number" name="difficulty" id="difficulty" value="{{ old('difficulty', $item->difficulty) }}" required
                                   step="0.01" min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('difficulty') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Valor decimal que representa el nivel de dificultad</p>
                            @error('difficulty')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contenido - Imagen de la matriz -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contenido del Ítem</label>

                            <!-- Imagen de la matriz -->
                            <div class="mb-4 p-4 border border-gray-300 rounded">
                                <label for="matrix_image" class="block text-sm font-medium text-gray-700">Imagen de la Matriz</label>

                                @if(isset($item->content['matrix_image']))
                                    <div class="mt-2 mb-3">
                                        <p class="text-xs text-gray-500 mb-1">Imagen actual:</p>
                                        <img src="{{ asset('storage/' . $item->content['matrix_image']) }}" alt="Matriz actual" class="max-w-xs rounded border">
                                    </div>
                                @endif

                                <input type="file" name="matrix_image" id="matrix_image" accept="image/*"
                                       class="mt-1 block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100
                                              @error('matrix_image') border-red-500 @enderror">
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ isset($item->content['matrix_image']) ? 'Sube una nueva imagen para reemplazar la actual' : 'Formatos: JPG, PNG, GIF, WEBP (Máx. 2MB)' }}
                                </p>
                                @error('matrix_image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <!-- Preview -->
                                <div id="matrix_preview" class="mt-3 hidden">
                                    <p class="text-xs text-gray-500 mb-1">Nueva imagen:</p>
                                    <img src="" alt="Preview" class="max-w-xs rounded border">
                                </div>
                            </div>

                            <!-- Opciones de respuesta -->
                            <div class="mb-4 p-4 border border-gray-300 rounded">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Opciones de Respuesta</label>

                                @for($i = 1; $i <= 6; $i++)
                                    <div class="mb-3 pb-3 border-b border-gray-200 last:border-b-0">
                                        <label for="option_{{ $i }}" class="block text-sm font-medium text-gray-600">Opción {{ $i }}</label>

                                        @if(isset($item->content['options'][$i]))
                                            <div class="mt-2 mb-2">
                                                <p class="text-xs text-gray-500 mb-1">Imagen actual:</p>
                                                <img src="{{ asset('storage/' . $item->content['options'][$i]) }}" alt="Opción {{ $i }} actual" class="max-w-xs rounded border">
                                            </div>
                                        @endif

                                        <input type="file" name="option_{{ $i }}" id="option_{{ $i }}" accept="image/*"
                                               class="mt-1 block w-full text-sm text-gray-500
                                                      file:mr-4 file:py-2 file:px-4
                                                      file:rounded file:border-0
                                                      file:text-sm file:font-semibold
                                                      file:bg-green-50 file:text-green-700
                                                      hover:file:bg-green-100
                                                      @error('option_'.$i) border-red-500 @enderror">
                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ isset($item->content['options'][$i]) ? 'Sube una nueva imagen para reemplazar la actual' : 'Sube una imagen para esta opción' }}
                                        </p>
                                        @error('option_'.$i)
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror

                                        <!-- Preview -->
                                        <div id="option_{{ $i }}_preview" class="mt-2 hidden">
                                            <p class="text-xs text-gray-500 mb-1">Nueva imagen:</p>
                                            <img src="" alt="Preview opción {{ $i }}" class="max-w-xs rounded border">
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Respuesta Correcta -->
                        <div class="mb-4">
                            <label for="correct_answer" class="block text-sm font-medium text-gray-700">Respuesta Correcta *</label>
                            <input type="text" name="correct_answer" id="correct_answer" value="{{ old('correct_answer', $item->correct_answer) }}" required
                                   placeholder="Ej: 1, 2, 3, etc."
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('correct_answer') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Indica el número de la opción correcta (1, 2, 3, etc.)</p>
                            @error('correct_answer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estado Activo -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $item->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Ítem activo (se puede usar en evaluaciones)</span>
                            </label>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.items.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Actualizar Ítem
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Preview de imagen de matriz
        document.getElementById('matrix_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('matrix_preview');
                    preview.querySelector('img').src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        // Preview de opciones
        for (let i = 1; i <= 6; i++) {
            document.getElementById(`option_${i}`).addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById(`option_${i}_preview`);
                        preview.querySelector('img').src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    </script>
    @endpush
</x-app-layout>
