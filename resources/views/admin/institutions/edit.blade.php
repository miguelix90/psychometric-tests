<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Institución
            </h2>
            <a href="{{ route('admin.institutions.index') }}"
               class="text-gray-600 hover:text-gray-900">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Código de acceso (no editable) -->
                    <div class="mb-6 bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">Código de Acceso</p>
                        <p class="font-mono text-xl font-bold text-gray-900">{{ $institution->access_code }}</p>
                        <p class="text-xs text-gray-500 mt-1">Este código no puede ser modificado</p>
                    </div>

                    <!-- Formulario -->
                    <form method="POST" action="{{ route('admin.institutions.update', $institution) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nombre -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $institution->name) }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo -->
                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo <span class="text-red-500">*</span>
                            </label>
                            <select name="type"
                                    id="type"
                                    required
                                    {{ $institution->type === \App\Enums\InstitutionType::SOCIESCUELA ? 'disabled' : '' }}
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('type') border-red-500 @enderror">
                                @foreach($types as $type)
                                    <option value="{{ $type->value }}" {{ old('type', $institution->type->value) == $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @if($institution->type === \App\Enums\InstitutionType::SOCIESCUELA)
                                <input type="hidden" name="type" value="{{ $institution->type->value }}">
                                <p class="mt-1 text-sm text-gray-500">El tipo de Sociescuela no puede ser modificado</p>
                            @endif
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nombre de Contacto -->
                        <div class="mb-4">
                            <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de Contacto <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="contact_name"
                                   id="contact_name"
                                   value="{{ old('contact_name', $institution->contact_name) }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('contact_name') border-red-500 @enderror">
                            @error('contact_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   value="{{ old('email', $institution->email) }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Usos Disponibles -->
                        <div class="mb-6">
                            <label for="available_uses" class="block text-sm font-medium text-gray-700 mb-2">
                                Usos Disponibles <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="available_uses"
                                   id="available_uses"
                                   value="{{ old('available_uses', $institution->available_uses) }}"
                                   min="0"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('available_uses') border-red-500 @enderror">
                            @error('available_uses')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Número de evaluaciones que la institución puede realizar
                            </p>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.institutions.index') }}"
                               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                                Actualizar Institución
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
