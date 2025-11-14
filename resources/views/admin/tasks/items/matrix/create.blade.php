<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Crear Ítem Matrix
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Tarea: {{ $task->name }}
                </p>
            </div>
            <a href="{{ route('admin.tasks.items.index', $task) }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver al Listado
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.tasks.items.store', $task) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Código -->
                        <div class="mb-4">
                            <label for="code" class="block text-sm font-medium text-gray-700">Código del Ítem *</label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                   placeholder="Ej: MAT_001"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('code') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Formato recomendado: CODIGO_NNN (Ej: MAT_001, NUM_015)</p>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dificultad -->
                        <div class="mb-4">
                            <label for="difficulty" class="block text-sm font-medium text-gray-700">Dificultad *</label>
                            <input type="number" name="difficulty" id="difficulty" value="{{ old('difficulty') }}" required
                                   step="0.01" min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('difficulty') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Valor decimal que representa el nivel de dificultad (0.0 - 10.0)</p>
                            @error('difficulty')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Imagen de la matriz -->
                        <div class="mb-4 p-4 border border-gray-300 rounded">
                            <label for="matrix_image" class="block text-sm font-medium text-gray-700">Imagen de la Matriz *</label>
                            <input type="file" name="matrix_image" id="matrix_image" accept="image/*" required
                                   class="mt-1 block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-blue-50 file:text-blue-700
                                          hover:file:bg-blue-100
                                          @error('matrix_image') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Sube la imagen de la matriz (SVG, PNG, JPG). Máx: 2MB</p>
                            @error('matrix_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Preview -->
                            <div id="matrix_preview" class="mt-2 hidden">
                                <p class="text-xs text-gray-500 mb-1">Vista previa:</p>
                                <img src="" alt="Preview matriz" class="max-w-xs rounded border">
                            </div>
                        </div>

                        <!-- Opciones de respuesta -->
                        <div class="mb-4 p-4 border border-gray-300 rounded">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Opciones de Respuesta</label>
                            <p class="text-sm text-gray-500 mb-3">Sube las imágenes de las opciones de respuesta (máximo 8 opciones)</p>

                            @for($i = 1; $i <= 8; $i++)
                                <div class="mb-3 p-3 bg-gray-50 rounded">
                                    <label for="option_{{ $i }}" class="block text-sm font-medium text-gray-700">Opción {{ $i }}</label>
                                    <input type="file" name="option_{{ $i }}" id="option_{{ $i }}" accept="image/*"
                                           class="mt-1 block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-green-50 file:text-green-700
                                                  hover:file:bg-green-100
                                                  @error('option_'.$i) border-red-500 @enderror">
                                    @error('option_'.$i)
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                    <!-- Preview -->
                                    <div id="option_{{ $i }}_preview" class="mt-2 hidden">
                                        <img src="" alt="Preview opción {{ $i }}" class="max-w-xs h-24 object-contain rounded border">
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <!-- Respuesta Correcta -->
                        <div class="mb-4">
                            <label for="correct_answer" class="block text-sm font-medium text-gray-700">Respuesta Correcta *</label>
                            <input type="text" name="correct_answer" id="correct_answer" value="{{ old('correct_answer') }}" required
                                   placeholder="Ej: 1, 2, 3, etc."
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('correct_answer') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Indica el número de la opción correcta (1-8)</p>
                            @error('correct_answer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estado Activo -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Ítem activo (se puede usar en evaluaciones)</span>
                            </label>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.tasks.items.index', $task) }}"
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Crear Ítem
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Preview de imagen de matriz
            const matrixInput = document.getElementById('matrix_image');
            const matrixPreview = document.getElementById('matrix_preview');

            matrixInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        matrixPreview.querySelector('img').src = e.target.result;
                        matrixPreview.classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Preview de opciones (números 1-8)
            for (let i = 1; i <= 8; i++) {
                const optionInput = document.getElementById(`option_${i}`);
                const optionPreview = document.getElementById(`option_${i}_preview`);

                if (optionInput && optionPreview) {
                    optionInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                optionPreview.querySelector('img').src = e.target.result;
                                optionPreview.classList.remove('hidden');
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
