<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Participantes') }}
            </h2>
            <a href="{{ route('professor.participants.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nuevo Participante
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Información sobre privacidad -->
            <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
                <p class="text-sm">
                    🔒 <strong>Protección de datos:</strong> Por privacidad, no se almacenan nombres ni fechas de nacimiento.
                    Los participantes se identifican mediante códigos únicos (IUG/IUC).
                </p>
            </div>

            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('professor.participants.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Buscar por IUC</label>
                                <input type="text"
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Código IUC"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sexo</label>
                                <select name="sex"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Todos</option>
                                    <option value="M" {{ request('sex') === 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ request('sex') === 'F' ? 'selected' : '' }}>Femenino</option>
                                    <option value="O" {{ request('sex') === 'O' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>

                            @if(isset($institutions))
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Institución</label>
                                <select name="institution_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Todas</option>
                                    @foreach($institutions as $institution)
                                        <option value="{{ $institution->id }}" {{ request('institution_id') == $institution->id ? 'selected' : '' }}>
                                            {{ $institution->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                Filtrar
                            </button>
                            <a href="{{ route('professor.participants.index') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de participantes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Edad
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sexo
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    IUC (parcial)
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Institución
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Creado por
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($participants as $participant)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ $participant->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $participant->getFormattedAge() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $participant->sex->label() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                        {{ substr($participant->iuc, 0, 12) }}...
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $participant->institution->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $participant->createdBy->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('professor.participants.show', $participant) }}"
                                           class="text-blue-600 hover:text-blue-900">Ver</a>
                                        <a href="{{ route('professor.participants.assign-battery', $participant) }}"
                                            class="text-green-600 hover:text-green-900"
                                            title="Asignar batería">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        No hay participantes registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $participants->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
