<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Instituciones
            </h2>
            <a href="{{ route('admin.institutions.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-150">
                Nueva Institución
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

            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.institutions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                        <!-- Buscar por nombre -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Nombre de institución"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Filtro por tipo -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <select name="type"
                                    id="type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todos</option>
                                @foreach(\App\Enums\InstitutionType::cases() as $type)
                                    <option value="{{ $type->value }}" {{ request('type') == $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por usos -->
                        <div>
                            <label for="uses_filter" class="block text-sm font-medium text-gray-700 mb-1">Usos</label>
                            <select name="uses_filter"
                                    id="uses_filter"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todos</option>
                                <option value="none" {{ request('uses_filter') == 'none' ? 'selected' : '' }}>Sin usos (0)</option>
                                <option value="low" {{ request('uses_filter') == 'low' ? 'selected' : '' }}>Bajos (1-10)</option>
                                <option value="medium" {{ request('uses_filter') == 'medium' ? 'selected' : '' }}>Medios (11-50)</option>
                                <option value="high" {{ request('uses_filter') == 'high' ? 'selected' : '' }}>Altos (50+)</option>
                            </select>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-end gap-2">
                            <button type="submit"
                                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                Filtrar
                            </button>
                            <a href="{{ route('admin.institutions.index') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de instituciones -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($institutions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Nombre
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Tipo
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Código Acceso
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Usos Disponibles
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Usuarios
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Participantes
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($institutions as $institution)
                                        <tr>
                                            <!-- Nombre -->
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $institution->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $institution->contact_name }}
                                                </div>
                                            </td>

                                            <!-- Tipo -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $institution->type->label() }}
                                                </span>
                                            </td>

                                            <!-- Código de acceso -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="font-mono text-sm font-semibold text-gray-900">
                                                    {{ $institution->access_code }}
                                                </span>
                                            </td>

                                            <!-- Usos disponibles -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold
                                                    {{ $institution->available_uses <= 0 ? 'text-red-600' : '' }}
                                                    {{ $institution->available_uses > 0 && $institution->available_uses <= 10 ? 'text-yellow-600' : '' }}
                                                    {{ $institution->available_uses > 10 ? 'text-green-600' : '' }}">
                                                    {{ $institution->available_uses }}
                                                </div>
                                            </td>

                                            <!-- Usuarios -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $institution->users_count }}
                                            </td>

                                            <!-- Participantes -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $institution->participants_count }}
                                            </td>

                                            <!-- Acciones -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center gap-2">
                                                    <!-- Ver -->
                                                    <a href="{{ route('admin.institutions.show', $institution) }}"
                                                       class="text-indigo-600 hover:text-indigo-900"
                                                       title="Ver detalles">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </a>

                                                    <!-- Editar -->
                                                    <a href="{{ route('admin.institutions.edit', $institution) }}"
                                                       class="text-blue-600 hover:text-blue-900"
                                                       title="Editar">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>

                                                    <!-- Eliminar (solo si no es Sociescuela) -->
                                                    @if($institution->type !== \App\Enums\InstitutionType::SOCIESCUELA)
                                                        <form method="POST"
                                                              action="{{ route('admin.institutions.destroy', $institution) }}"
                                                              class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="text-red-600 hover:text-red-900"
                                                                    title="Eliminar"
                                                                    onclick="return confirm('¿Eliminar esta institución? Esta acción no se puede deshacer.')">
                                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-6">
                            {{ $institutions->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500">No hay instituciones registradas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
