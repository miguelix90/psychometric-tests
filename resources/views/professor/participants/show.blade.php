<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle del Participante') }}
            </h2>
            <a href="{{ route('professor.participants.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

            <!-- Aviso de privacidad -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Protección de Datos</h3>
                        <p class="mt-1 text-sm text-blue-700">
                            Por privacidad, no se almacenan datos personales (nombre, apellidos, fecha de nacimiento).
                            El participante se identifica únicamente mediante códigos únicos generados.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información Principal -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Participante</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">ID del Sistema</label>
                            <p class="mt-1 text-lg text-gray-900">
                                #{{ $participant->id }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Sexo</label>
                            <p class="mt-1 text-lg text-gray-900">
                                {{ $participant->sex->label() }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Edad</label>
                            <p class="mt-1 text-lg text-gray-900">
                                {{ $participant->getFormattedAge() }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Edad en Meses</label>
                            <p class="mt-1 text-lg text-gray-900">
                                {{ $participant->age_months }} meses
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Identificadores -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Identificadores Únicos</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">IUG (Identificador Único Global)</label>
                            <div class="mt-1 flex items-center">
                                <code class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded font-mono text-sm break-all">
                                    {{ $participant->iug }}
                                </code>
                                <button onclick="copyToClipboard('{{ $participant->iug }}')"
                                        class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                                    Copiar
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Identificador universal único generado a partir de datos personales (no almacenados)
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">IUC (Identificador Único Centro)</label>
                            <div class="mt-1 flex items-center">
                                <code class="flex-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded font-mono text-sm break-all">
                                    {{ $participant->iuc }}
                                </code>
                                <button onclick="copyToClipboard('{{ $participant->iuc }}')"
                                        class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                                    Copiar
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Identificador único dentro de la institución ({{ $participant->institution->access_code }})
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Institucional -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Institucional</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Institución</label>
                            <p class="mt-1 text-lg text-gray-900">
                                {{ $participant->institution->name }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Código de Acceso</label>
                            <p class="mt-1 text-lg text-gray-900 font-mono">
                                {{ $participant->institution->access_code }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Creado por</label>
                            <p class="mt-1 text-lg text-gray-900">
                                {{ $participant->createdBy->name }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Fecha de Registro</label>
                            <p class="mt-1 text-lg text-gray-900">
                                {{ $participant->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones Peligrosas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-red-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-red-600 mb-4">Zona de Peligro</h3>

                    <form method="POST"
                          action="{{ route('professor.participants.destroy', $participant) }}"
                          onsubmit="return confirm('¿Estás seguro de que deseas eliminar este participante? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">
                                    Eliminar este participante permanentemente.
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Esta acción eliminará todos los datos asociados al participante.
                                </p>
                            </div>
                            <button type="submit"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Eliminar Participante
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Copiado al portapapeles');
            }, function(err) {
                console.error('Error al copiar: ', err);
            });
        }
    </script>
</x-app-layout>
