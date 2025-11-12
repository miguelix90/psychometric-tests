<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Código de Batería
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

                    <!-- Información del código -->
                    <div class="mb-6 bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Información del Código</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Código</p>
                                <p class="font-mono font-bold text-xl text-gray-900">{{ $batteryCode->code }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Batería</p>
                                <p class="font-semibold text-gray-900">{{ $batteryCode->battery->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Estado</p>
                                @if ($batteryCode->isExpired())
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Expirado
                                    </span>
                                @elseif (!$batteryCode->is_active)
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactivo
                                    </span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Expira</p>
                                <p class="text-sm text-gray-900">{{ $batteryCode->expires_at->format('d/m/Y H:i') }}</p>
                                <p class="text-xs text-gray-500">{{ $batteryCode->expires_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Usos actuales -->
                    <div class="mb-6 bg-indigo-50 border-l-4 border-indigo-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-indigo-800 mb-2">
                                    Usos Actuales
                                </h3>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-2xl font-bold text-indigo-900">
                                            {{ $batteryCode->current_uses }} / {{ $batteryCode->max_uses }}
                                        </p>
                                        <p class="text-sm text-indigo-700">
                                            {{ $batteryCode->max_uses - $batteryCode->current_uses }} usos disponibles
                                        </p>
                                    </div>
                                    <div class="w-32">
                                        <div class="w-full bg-indigo-200 rounded-full h-3">
                                            <div class="bg-indigo-600 h-3 rounded-full"
                                                 style="width: {{ $batteryCode->max_uses > 0 ? ($batteryCode->current_uses / $batteryCode->max_uses * 100) : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Advertencia sobre límite -->
                    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    Restricción de Edición
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>El nuevo límite de usos debe ser mayor o igual a los usos actuales ({{ $batteryCode->current_uses }}). No puedes reducirlo por debajo de este número.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <form method="POST" action="{{ route('professor.battery-codes.update', $batteryCode) }}">
                        @csrf
                        @method('PUT')

                        <!-- Límite de Usos -->
                        <div class="mb-6">
                            <label for="max_uses" class="block text-sm font-medium text-gray-700 mb-2">
                                Nuevo Límite de Usos <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   name="max_uses"
                                   id="max_uses"
                                   value="{{ old('max_uses', $batteryCode->max_uses) }}"
                                   min="{{ $batteryCode->current_uses }}"
                                   max="1000"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('max_uses') border-red-500 @enderror">
                            @error('max_uses')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Mínimo: {{ $batteryCode->current_uses }} (usos actuales) | Máximo: 1000
                            </p>
                        </div>

                        <!-- Preview del cambio -->
                        <div class="mb-6 bg-blue-50 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">Vista Previa del Cambio</h4>
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <p class="text-gray-600">Límite actual:</p>
                                    <p class="font-bold text-gray-900">{{ $batteryCode->max_uses }} usos</p>
                                </div>
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                                <div>
                                    <p class="text-gray-600">Nuevo límite:</p>
                                    <p class="font-bold text-indigo-600" id="preview-max-uses">{{ old('max_uses', $batteryCode->max_uses) }} usos</p>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-blue-200">
                                <p class="text-xs text-blue-700">
                                    <strong>Usos disponibles después del cambio:</strong>
                                    <span id="preview-available-uses">
                                        {{ old('max_uses', $batteryCode->max_uses) - $batteryCode->current_uses }}
                                    </span>
                                </p>
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
                                Actualizar Límite
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript para preview en tiempo real -->
    <script>
        document.getElementById('max_uses').addEventListener('input', function() {
            const newMaxUses = parseInt(this.value) || {{ $batteryCode->current_uses }};
            const currentUses = {{ $batteryCode->current_uses }};
            const availableUses = newMaxUses - currentUses;

            document.getElementById('preview-max-uses').textContent = newMaxUses + ' usos';
            document.getElementById('preview-available-uses').textContent = availableUses;
        });
    </script>
</x-app-layout>
