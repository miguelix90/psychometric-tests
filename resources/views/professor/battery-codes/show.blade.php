<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detalle del Código: {{ $batteryCode->code }}
            </h2>
            <a href="{{ route('professor.battery-codes.index') }}"
               class="text-gray-600 hover:text-gray-900">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Mensajes -->
            @if (session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Información del código -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Código -->
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Código</p>
                            <div class="flex items-center gap-2">
                                <p class="font-mono text-2xl font-bold text-gray-900">{{ $batteryCode->code }}</p>
                                <button onclick="copyCodeUrl('{{ route('test.battery-code.form', $batteryCode->code) }}')"
                                        class="text-indigo-600 hover:text-indigo-900"
                                        title="Copiar URL">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Batería -->
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Batería</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $batteryCode->battery->name }}</p>
                            <p class="text-xs text-gray-500">{{ $batteryCode->battery->type->label() }}</p>
                        </div>

                        <!-- Estado -->
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Estado</p>
                            @if ($batteryCode->isExpired())
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                    Expirado
                                </span>
                            @elseif (!$batteryCode->is_active)
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactivo
                                </span>
                            @elseif (!$batteryCode->hasUsesAvailable())
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Sin usos
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    Activo
                                </span>
                            @endif
                        </div>

                        <!-- Usos -->
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Usos</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $batteryCode->current_uses }} / {{ $batteryCode->max_uses }}</p>
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div class="bg-indigo-600 h-2 rounded-full"
                                     style="width: {{ $batteryCode->max_uses > 0 ? ($batteryCode->current_uses / $batteryCode->max_uses * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="mt-6 pt-6 border-t border-gray-200 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Creado</p>
                            <p class="font-semibold text-gray-900">{{ $batteryCode->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Expira</p>
                            <p class="font-semibold text-gray-900">{{ $batteryCode->expires_at->format('d/m/Y H:i') }}</p>
                            <p class="text-xs text-gray-500">{{ $batteryCode->expires_at->diffForHumans() }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Creado por</p>
                            <p class="font-semibold text-gray-900">{{ $batteryCode->createdBy->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Institución</p>
                            <p class="font-semibold text-gray-900">{{ $batteryCode->institution->name }}</p>
                        </div>
                    </div>

                    <!-- URL completa -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-500 mb-2">URL de acceso</p>
                        <div class="flex items-center gap-2">
                            <input type="text"
                                   id="code-url"
                                   value="{{ route('test.battery-code.form', $batteryCode->code) }}"
                                   readonly
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50 font-mono">
                            <button onclick="copyCodeUrl('{{ route('test.battery-code.form', $batteryCode->code) }}')"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm">
                                Copiar URL
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sesiones asociadas a este código -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Sesiones Creadas con este Código ({{ $batteryCode->testSessions->count() }})
                    </h3>

                    @if($batteryCode->testSessions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Participante
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Estado
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Creada
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Iniciada
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Completada
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($batteryCode->testSessions as $session)
                                        <tr>
                                            <!-- Participante -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm">
                                                    <div class="font-mono text-xs text-gray-500">
                                                        {{ substr($session->participant->iuc, 0, 12) }}...
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $session->participant->getFormattedAge() }} - {{ $session->participant->sex->label() }}
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Estado -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $session->status->color() === 'gray' ? 'bg-gray-100 text-gray-800' : '' }}
                                                    {{ $session->status->color() === 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $session->status->color() === 'green' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $session->status->color() === 'red' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ $session->status->label() }}
                                                </span>
                                            </td>

                                            <!-- Creada -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $session->created_at->format('d/m/Y H:i') }}
                                            </td>

                                            <!-- Iniciada -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $session->started_at ? $session->started_at->format('d/m/Y H:i') : '-' }}
                                            </td>

                                            <!-- Completada -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $session->completed_at ? $session->completed_at->format('d/m/Y H:i') : '-' }}
                                            </td>

                                            <!-- Acciones -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                @if($session->isPending() || $session->isInProgress())
                                                    <form method="POST"
                                                          action="{{ route('professor.test-sessions.cancel', $session) }}"
                                                          class="inline">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit"
                                                                class="text-red-600 hover:text-red-900"
                                                                onclick="return confirm('¿Cancelar esta sesión? Se recuperará el uso.')">
                                                            Cancelar
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No hay sesiones creadas con este código todavía.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyCodeUrl(url) {
            const tempInput = document.createElement('input');
            tempInput.value = url;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            alert('URL copiada al portapapeles');
        }
    </script>
</x-app-layout>
