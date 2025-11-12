<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Asignar Batería a Participante
            </h2>
            <a href="{{ route('professor.participants.show', $participant) }}"
               class="text-gray-600 hover:text-gray-900">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <!-- Información del participante -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Información del Participante</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">IUC</p>
                            <p class="font-mono text-xs font-semibold text-gray-900">{{ substr($participant->iuc, 0, 12) }}...</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Edad</p>
                            <p class="font-semibold text-gray-900">{{ $participant->getFormattedAge() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Sexo</p>
                            <p class="font-semibold text-gray-900">{{ $participant->sex->label() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Institución</p>
                            <p class="font-semibold text-gray-900">{{ $participant->institution->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sesiones activas -->
            @if($activeSessions->count() > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                Sesiones Activas
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Este participante tiene las siguientes baterías pendientes o en progreso:</p>
                                <ul class="list-disc list-inside mt-2">
                                    @foreach($activeSessions as $session)
                                        <li>{{ $session->battery->name }} - <span class="font-semibold">{{ $session->status->label() }}</span></li>
                                    @endforeach
                                </ul>
                                <p class="mt-2">No podrás asignar estas baterías hasta que completen o se cancelen las sesiones actuales.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Usos disponibles -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">
                            Usos Disponibles
                        </h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>
                                La institución <strong>{{ $participant->institution->name }}</strong> tiene
                                <strong>{{ $participant->institution->available_uses }} usos disponibles</strong>.
                            </p>
                            <p class="mt-1">Se descontará 1 uso al asignar la batería.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de asignación -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Seleccionar Batería</h3>

                    <!-- Mensajes de error -->
                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    @foreach ($errors->all() as $error)
                                        <p class="text-sm text-red-700">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('professor.participants.store-assignment', $participant) }}">
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
                                    @php
                                        // Verificar si ya tiene sesión activa de esta batería
                                        $hasActiveSession = $activeSessions->contains('battery_id', $battery->id);
                                    @endphp
                                    <option value="{{ $battery->id }}"
                                            {{ old('battery_id') == $battery->id ? 'selected' : '' }}
                                            {{ $hasActiveSession ? 'disabled' : '' }}>
                                        {{ $battery->name }} ({{ $battery->type->label() }})
                                        {{ $hasActiveSession ? '- Ya asignada' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('battery_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Selecciona la batería que el participante deberá completar.
                            </p>
                        </div>

                        <!-- Información sobre la batería seleccionada -->
                        <div id="battery-info" class="hidden mb-6 bg-gray-50 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Información de la Batería</h4>
                            <div id="battery-description" class="text-sm text-gray-600"></div>
                            <div id="battery-tasks" class="mt-2 text-sm text-gray-600"></div>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('professor.participants.show', $participant) }}"
                               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                                Asignar Batería
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript para mostrar info de la batería -->
    <script>
        const batteries = @json($batteries);
        const batterySelect = document.getElementById('battery_id');
        const batteryInfo = document.getElementById('battery-info');
        const batteryDescription = document.getElementById('battery-description');
        const batteryTasks = document.getElementById('battery-tasks');

        batterySelect.addEventListener('change', function() {
            const batteryId = parseInt(this.value);
            const battery = batteries.find(b => b.id === batteryId);

            if (battery) {
                batteryInfo.classList.remove('hidden');
                batteryDescription.innerHTML = `<strong>Descripción:</strong> ${battery.description || 'Sin descripción'}`;
                batteryTasks.innerHTML = `<strong>Tareas:</strong> ${battery.tasks ? battery.tasks.length : 0} tareas`;
            } else {
                batteryInfo.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
