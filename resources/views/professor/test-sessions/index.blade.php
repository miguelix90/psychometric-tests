<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard de Sesiones
        </h2>
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

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gray-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gray-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Pendientes</p>
                                <p class="text-2xl font-semibold text-gray-600">{{ $stats['pending'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">En Progreso</p>
                                <p class="text-2xl font-semibold text-blue-600">{{ $stats['in_progress'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Completadas</p>
                                <p class="text-2xl font-semibold text-green-600">{{ $stats['completed'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('professor.test-sessions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                        <!-- Búsqueda por IUC -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar por IUC</label>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Código IUC del participante"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Filtro por estado -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select name="status"
                                    id="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todos</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activas (Pendiente + En Progreso)</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completadas</option>
                                <option value="abandoned" {{ request('status') == 'abandoned' ? 'selected' : '' }}>Abandonadas</option>
                            </select>
                        </div>

                        <!-- Filtro por batería -->
                        <div>
                            <label for="battery_id" class="block text-sm font-medium text-gray-700 mb-1">Batería</label>
                            <select name="battery_id"
                                    id="battery_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todas</option>
                                @foreach($batteries as $battery)
                                    <option value="{{ $battery->id }}" {{ request('battery_id') == $battery->id ? 'selected' : '' }}>
                                        {{ $battery->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por tipo de asignación -->
                        <div>
                            <label for="assignment_type" class="block text-sm font-medium text-gray-700 mb-1">Asignación</label>
                            <select name="assignment_type"
                                    id="assignment_type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Todas</option>
                                <option value="direct" {{ request('assignment_type') == 'direct' ? 'selected' : '' }}>Directa</option>
                                <option value="code" {{ request('assignment_type') == 'code' ? 'selected' : '' }}>Por Código</option>
                            </select>
                        </div>

                        <!-- Fecha desde -->
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                            <input type="date"
                                   name="date_from"
                                   id="date_from"
                                   value="{{ request('date_from') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Fecha hasta -->
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                            <input type="date"
                                   name="date_to"
                                   id="date_to"
                                   value="{{ request('date_to') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Botones (colspan 2) -->
                        <div class="md:col-span-2 flex items-end gap-2">
                            <button type="submit"
                                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                Filtrar
                            </button>
                            <a href="{{ route('professor.test-sessions.index') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de sesiones -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($sessions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Participante</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batería</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asignación</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creada</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Iniciada</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completada</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($sessions as $session)
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

                                            <!-- Batería -->
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $session->battery->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $session->battery->type->label() }}</div>
                                            </td>

                                            <!-- Estado -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $session->status === \App\Enums\SessionStatus::PENDING ? 'bg-gray-100 text-gray-800' : '' }}
                                                    {{ $session->status === \App\Enums\SessionStatus::IN_PROGRESS ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $session->status === \App\Enums\SessionStatus::COMPLETED ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $session->status === \App\Enums\SessionStatus::ABANDONED ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ $session->status->label() }}
                                                </span>
                                            </td>

                                            <!-- Tipo de Asignación -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($session->battery_code_id)
                                                    <span class="text-purple-600 font-medium">Por Código</span>
                                                    <div class="text-xs text-gray-500">{{ $session->batteryCode->code }}</div>
                                                @else
                                                    <span class="text-indigo-600 font-medium">Directa</span>
                                                    <div class="text-xs text-gray-500">{{ $session->assignedBy?->name ?? '-' }}</div>
                                                @endif
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

                        <!-- Paginación -->
                        <div class="mt-6">
                            {{ $sessions->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay sesiones</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                No se encontraron sesiones con los filtros aplicados.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
