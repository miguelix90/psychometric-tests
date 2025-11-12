<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Generar Código de Batería
            </h2>
            <a href="{{ route('professor.battery-codes.index') }}"
               class="text-gray-600 hover:text-gray-900">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Descripción -->
                    <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">
                                    ¿Cómo funcionan los códigos de batería?
                                </h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Se genera un código único de 7 caracteres</li>
                                        <li>El código caduca automáticamente en 8 horas</li>
                                        <li>Puedes configurar cuántas veces se puede usar</li>
                                        <li>Los participantes acceden mediante URL directa</li>
                                        <li>Puedes desactivar el código manualmente en cualquier momento</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form method="POST" action="{{ route('professor.battery-codes.store') }}">
                        @csrf

                        <!-- Seleccionar Batería -->
                        <div class="mb-6">
                            <label for="battery_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Batería <span class="text-red-500">*</span>
                            </label>
                            <select name="battery_id"
                                    id="battery_id"
                                    required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('battery_id') border-red-500 @enderror">
                                <option value="">Selecciona una batería</option>
                                @foreach ($batteries as $battery)
                                    <option value="{{ $battery->id }}" {{ old('battery_id') == $battery->id ? 'selected' : '' }}>
                                        {{ $battery->name }} ({{ $battery->type->label() }})
                                    </option>
                                @endforeach
                            </select>
                            @error('battery_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Selecciona la batería que los participantes realizarán con este código.
                            </p>
                        </div>

                        <!-- Límite de Usos -->
                        <div class="mb-6">
                            <label for="max_uses" class="block text-sm font-medium text-gray-700 mb-2">
                                Límite de Usos <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="max_uses"
                                   id="max_uses"
                                   value="{{ old('max_uses', 10) }}"
                                   min="1"
                                   max="1000"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('max_uses') border-red-500 @enderror">
                            @error('max_uses')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Número máximo de participantes que pueden usar este código (1-1000).
                            </p>
                        </div>

                        <!-- Información sobre expiración -->
                        <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Expiración Automática
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>El código se desactivará automáticamente en <strong>8 horas</strong> desde su creación. Puedes desactivarlo manualmente antes si lo deseas.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información sobre usos -->
                        <div class="mb-6 bg-purple-50 border-l-4 border-purple-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-purple-800">
                                        Descuento de Usos
                                    </h3>
                                    <div class="mt-2 text-sm text-purple-700">
                                        <p>Cada vez que un participante use este código, se descontará <strong>1 uso</strong> tanto del código como de los usos disponibles de tu institución.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('professor.battery-codes.index') }}"
                               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                                Generar Código
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
