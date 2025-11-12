<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Códigos de Batería
            </h2>
            <a href="{{ route('professor.battery-codes.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150">
                Generar Código
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Mensajes de éxito -->
            @if (session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                            @if (session('code_url'))
                                <div class="mt-2 flex items-center gap-2">
                                    <input type="text"
                                           id="code-url"
                                           value="{{ session('code_url') }}"
                                           readonly
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm bg-gray-50">
                                    <button onclick="copyToClipboard()"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm">
                                        Copiar URL
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('professor.battery-codes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                        <!-- Buscar por código -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar Código</label>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Ej: ABC1234"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Filtro por batería -->
                        <div>
                            <label for="battery_id" class="block text-sm font-medium text-gray-700 mb-1">Batería</label>
                            <select name="battery_id"
                                    id="battery_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todas</option>
                                @foreach ($batteries as $battery)
                                    <option value="{{ $battery->id }}" {{ request('battery_id') == $battery->id ? 'selected' : '' }}>
                                        {{ $battery->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por estado -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select name="status"
                                    id="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todos</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expirado</option>
                            </select>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-end gap-2">
                            <button type="submit"
                                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                Filtrar
                            </button>
                            <a href="{{ route('professor.battery-codes.index') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de códigos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($batteryCodes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Código
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Batería
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Usos
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Expira
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($batteryCodes as $code)
                                        <tr>
                                            <!-- Código -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-mono font-semibold text-lg text-gray-900">
                                                        {{ $code->code }}
                                                    </span>
                                                    <button onclick="copyCodeUrl('{{ route('test.battery-code.form', $code->code) }}')"
                                                            class="text-indigo-600 hover:text-indigo-900"
                                                            title="Copiar URL">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>

                                            <!-- Batería -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $code->battery->name }}</div>
                                            </td>

                                            <!-- Usos -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $code->current_uses }} / {{ $code->max_uses }}
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                                    <div class="bg-indigo-600 h-2 rounded-full"
                                                         style="width: {{ $code->max_uses > 0 ? ($code->current_uses / $code->max_uses * 100) : 0 }}%">
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Estado -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($code->isExpired())
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Expirado
                                                    </span>
                                                @elseif (!$code->is_active)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Inactivo
                                                    </span>
                                                @elseif (!$code->hasUsesAvailable())
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Sin usos
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Activo
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Expira -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $code->expires_at->format('d/m/Y H:i') }}
                                                <div class="text-xs text-gray-400">
                                                    {{ $code->expires_at->diffForHumans() }}
                                                </div>
                                            </td>

                                            <!-- Acciones -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center gap-2">
                                                    <!-- Editar límite -->
                                                    <a href="{{ route('professor.battery-codes.edit', $code) }}"
                                                       class="text-indigo-600 hover:text-indigo-900"
                                                       title="Editar límite de usos">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>

                                                    <!-- Activar/Desactivar -->
                                                    @if ($code->is_active && !$code->isExpired())
                                                        <form method="POST"
                                                              action="{{ route('professor.battery-codes.deactivate', $code) }}"
                                                              class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="text-yellow-600 hover:text-yellow-900"
                                                                    title="Desactivar"
                                                                    onclick="return confirm('¿Desactivar este código?')">
                                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @elseif (!$code->isExpired())
                                                        <form method="POST"
                                                              action="{{ route('professor.battery-codes.activate', $code) }}"
                                                              class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                    class="text-green-600 hover:text-green-900"
                                                                    title="Activar"
                                                                    onclick="return confirm('¿Activar este código?')">
                                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <!-- Ver detalles -->
                                                    <a href="{{ route('professor.battery-codes.show', $code) }}"
                                                    class="text-gray-600 hover:text-gray-900"
                                                    title="Ver detalles">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </a>

                                                    <!-- Eliminar -->
                                                    <form method="POST"
                                                          action="{{ route('professor.battery-codes.destroy', $code) }}"
                                                          class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="text-red-600 hover:text-red-900"
                                                                title="Eliminar"
                                                                onclick="return confirm('¿Eliminar este código? Esta acción no se puede deshacer.')">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-6">
                            {{ $batteryCodes->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay códigos</h3>
                            <p class="mt-1 text-sm text-gray-500">Comienza generando un código de batería.</p>
                            <div class="mt-6">
                                <a href="{{ route('professor.battery-codes.create') }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    Generar Código
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript para copiar URL -->
    <script>
        function copyToClipboard() {
            const input = document.getElementById('code-url');
            input.select();
            document.execCommand('copy');

            alert('URL copiada al portapapeles');
        }

        function copyCodeUrl(url) {
            const tempInput = document.createElement('input');
            tempInput.value = url;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            alert('URL copiada al portapapeles: ' + url);
        }
    </script>
</x-app-layout>
