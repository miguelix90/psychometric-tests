<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Participante') }}
            </h2>
            <a href="{{ route('professor.participants.show', $participant) }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensaje informativo -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-yellow-800">
                            No se pueden editar participantes
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p class="mb-2">
                                Por motivos de <strong>protección de datos</strong>, los datos personales (nombre, apellidos, fecha de nacimiento)
                                no se almacenan en la base de datos, por lo que no es posible editar un participante existente.
                            </p>
                            <p class="mb-2">
                                Los identificadores únicos (IUG e IUC) se generan a partir de los datos personales en el momento de la creación
                                y no pueden modificarse posteriormente.
                            </p>
                            <p>
                                Si necesitas corregir información de un participante, debes eliminarlo y crear uno nuevo con los datos correctos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del participante actual -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Actual</h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">ID</label>
                                <p class="mt-1 text-gray-900">#{{ $participant->id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Edad</label>
                                <p class="mt-1 text-gray-900">{{ $participant->getFormattedAge() }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Sexo</label>
                                <p class="mt-1 text-gray-900">{{ $participant->sex->label() }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Institución</label>
                                <p class="mt-1 text-gray-900">{{ $participant->institution->name }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">IUC</label>
                            <code class="block mt-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded font-mono text-sm break-all">
                                {{ $participant->iuc }}
                            </code>
                        </div>
                    </div>

                    <div class="mt-6 flex space-x-4">
                        <a href="{{ route('professor.participants.show', $participant) }}"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Ver Detalles Completos
                        </a>
                        <a href="{{ route('professor.participants.index') }}"
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
