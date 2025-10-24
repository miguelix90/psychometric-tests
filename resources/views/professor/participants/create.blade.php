<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nuevo Participante') }}
            </h2>
            <a href="{{ route('professor.participants.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('professor.participants.store') }}">
                        @csrf

                        <!-- Nombre -->
                        <div class="mb-4">
                            <label for="first_name" class="block text-sm font-medium text-gray-700">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="first_name"
                                   id="first_name"
                                   value="{{ old('first_name') }}"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('first_name') border-red-500 @enderror">
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Apellido -->
                        <div class="mb-4">
                            <label for="last_name" class="block text-sm font-medium text-gray-700">
                                Apellido <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="last_name"
                                   id="last_name"
                                   value="{{ old('last_name') }}"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('last_name') border-red-500 @enderror">
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha de Nacimiento -->
                        <div class="mb-4">
                            <label for="birth_date" class="block text-sm font-medium text-gray-700">
                                Fecha de Nacimiento <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   name="birth_date"
                                   id="birth_date"
                                   value="{{ old('birth_date') }}"
                                   max="{{ date('Y-m-d') }}"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('birth_date') border-red-500 @enderror">
                            @error('birth_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                La edad se calculará automáticamente a partir de esta fecha.
                            </p>
                        </div>

                        <!-- Sexo -->
                        <div class="mb-4">
                            <label for="sex" class="block text-sm font-medium text-gray-700">
                                Sexo <span class="text-red-500">*</span>
                            </label>
                            <select name="sex"
                                    id="sex"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('sex') border-red-500 @enderror">
                                <option value="">Seleccione...</option>
                                @foreach($sexOptions as $option)
                                    <option value="{{ $option->value }}" {{ old('sex') === $option->value ? 'selected' : '' }}>
                                        {{ $option->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sex')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Información adicional -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                            <h3 class="text-sm font-medium text-blue-900 mb-2">
                                ℹ️ Información sobre identificadores
                            </h3>
                            <p class="text-sm text-blue-700">
                                Al crear el participante, se generarán automáticamente:
                            </p>
                            <ul class="list-disc list-inside text-sm text-blue-700 mt-2">
                                <li><strong>IUG</strong> (Identificador Único Global): Basado en nombre, apellido y fecha de nacimiento</li>
                                <li><strong>IUC</strong> (Identificador Único Centro): IUG + código de tu institución</li>
                            </ul>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('professor.participants.index') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Crear Participante
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
