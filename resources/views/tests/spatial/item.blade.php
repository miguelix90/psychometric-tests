@extends('layouts.test')

@section('title', 'Ítem ' . $currentItemNumber . ' de ' . $totalItems)

@section('progress-bar')
    <div class="px-6 py-4">
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-lg font-semibold">{{ $testSessionTask->task->name }}</h1>
            <span class="text-sm text-gray-600">
                Ítem {{ $currentItemNumber }} de {{ $totalItems }}
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                 style="width: {{ ($currentItemNumber / $totalItems) * 100 }}%"></div>
        </div>
    </div>
@endsection

@section('content')
    {{-- Banner Modo Demo --}}
    @if(isset($demoMode) && $demoMode)
        <div class="fixed top-16 left-0 right-0 z-40 bg-yellow-500 text-black px-4 py-3 text-center font-bold shadow-lg">
            ⚠️ MODO DEMO - Las respuestas NO se guardan
            <form action="{{ route('admin.demo.exit') }}" method="POST" class="inline ml-4">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-semibold">
                    ❌ Salir del Demo
                </button>
            </form>
        </div>
    @endif

    <div class="max-w-6xl mx-auto space-y-8 {{ isset($demoMode) && $demoMode ? 'mt-20' : '' }}">

        <!-- Pregunta -->
        @if(isset($item->content['question_text']) && !empty($item->content['question_text']))
            <div class="bg-blue-50 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 text-center">
                    {{ $item->content['question_text'] }}
                </h2>
            </div>
        @endif

        <!-- Estímulo principal -->
        <div class="bg-white rounded-lg shadow-xl p-8 lg:p-12">
            <div class="flex justify-center">
                <img src="{{ Storage::url($item->content['stimulus_image']) }}"
                     alt="Estímulo"
                     class="max-w-full max-h-96 object-contain">
            </div>
        </div>

        <!-- Opciones de respuesta -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h3 class="text-lg font-semibold mb-6 text-center text-gray-800">
                Selecciona la opción correcta:
            </h3>

            @php
                $optionsCount = count($item->content['options']);
                // Determinar clase de grid según cantidad de opciones
                $gridClass = match(true) {
                    $optionsCount <= 2 => 'grid-cols-2',
                    $optionsCount == 3 => 'grid-cols-3',
                    $optionsCount == 4 => 'grid-cols-4',
                    $optionsCount == 5 => 'grid-cols-5',
                    $optionsCount >= 6 => 'grid-cols-6',
                    default => 'grid-cols-3'
                };
            @endphp

            <div class="grid {{ $gridClass }} gap-4 md:gap-6 mb-8 max-w-6xl mx-auto">
                @foreach($item->content['options'] as $optionLetter => $optionPath)
                    @if(!empty($optionPath))
                        <label class="cursor-pointer group">
                            <input type="radio"
                                   name="answer"
                                   value="{{ $optionLetter }}"
                                   class="hidden peer"
                                   required>
                            <div class="border-3 border-gray-300 rounded-xl p-3 md:p-4
                                        peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:shadow-lg
                                        hover:border-gray-400 hover:shadow-md
                                        transition-all duration-200">
                                <img src="{{ Storage::url($optionPath) }}"
                                     alt="Opción {{ $optionLetter }}"
                                     class="w-full mb-2 md:mb-3"
                                     loading="lazy">
                                <div class="text-center">
                                    <span class="inline-block px-3 py-1 md:px-4 md:py-2 rounded-full text-sm font-semibold
                                               bg-gray-100 text-gray-700
                                               group-has-[:checked]:bg-blue-600 group-has-[:checked]:text-white
                                               group-hover:bg-gray-200
                                               transition-colors">
                                        {{ $optionLetter }}
                                    </span>
                                </div>
                            </div>
                        </label>
                    @endif
                @endforeach
            </div>

            <!-- Botón siguiente -->
            <div class="text-center">
                <button type="button"
                        id="btnNext"
                        class="px-12 py-4 bg-blue-600 text-white text-lg font-semibold rounded-lg
                               hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed
                               transition-all duration-200 shadow-md hover:shadow-lg"
                        disabled>
                    Siguiente
                </button>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
<script>
    // Configuración
    const config = {
        @if(isset($demoMode) && $demoMode && isset($demoType) && $demoType === 'item')
            // Modo Demo - ITEM Individual
            taskId: {{ $testSessionTask->task_id }},
            itemId: {{ $item->id }},
            csrfToken: '{{ csrf_token() }}',
            submitUrl: '{{ route('admin.demo.item.response', ['item' => $item->id]) }}',
            demoMode: true,
            demoType: 'item'
        @elseif(isset($demoMode) && $demoMode && isset($demoType) && $demoType === 'battery')
            // Modo Demo - BATERÍA Completa
            taskId: {{ $testSessionTask->task_id }},
            itemId: {{ $item->id }},
            csrfToken: '{{ csrf_token() }}',
            submitUrl: '{{ route('admin.demo.battery.response', ['itemId' => $item->id]) }}',
            demoMode: true,
            demoType: 'battery'
        @elseif(isset($demoMode) && $demoMode)
            // Modo Demo - TAREA Completa
            taskId: {{ $testSessionTask->task_id }},
            itemId: {{ $item->id }},
            csrfToken: '{{ csrf_token() }}',
            submitUrl: '{{ route('admin.demo.spatial.response', ['itemId' => $item->id]) }}',
            demoMode: true,
            demoType: 'task'
        @else
            // Modo Normal
            taskId: {{ $testSessionTask->id }},
            itemId: {{ $item->id }},
            csrfToken: '{{ csrf_token() }}',
            submitUrl: '{{ route('test.spatial.item.submit', [$testSessionTask, $item]) }}',
            demoMode: false,
            demoType: null
        @endif
    };

    // Clase para manejar la presentación
    class SpatialPresentation {
        constructor(config) {
            this.config = config;
            this.startTime = null;
            this.beforeUnloadHandler = null;
            this.init();
        }

        init() {
            // Iniciar timer
            this.startTimer();

            // Escuchar cambios en selección
            document.querySelectorAll('input[name="answer"]').forEach(radio => {
                radio.addEventListener('change', () => this.toggleNextButton());
            });

            // Botón siguiente
            document.getElementById('btnNext').addEventListener('click', () => {
                this.submitResponse();
            });

            // Prevenir retroceso del navegador
            this.preventBackNavigation();

            // Advertir antes de salir (solo en modo normal)
            if (!this.config.demoMode) {
                this.warnBeforeUnload();
            }
        }

        startTimer() {
            this.startTime = Date.now();
        }

        getResponseTime() {
            if (!this.startTime) return 0;
            return Date.now() - this.startTime;
        }

        toggleNextButton() {
            const btnNext = document.getElementById('btnNext');
            const isValid = document.querySelector('input[name="answer"]:checked') !== null;
            btnNext.disabled = !isValid;
        }

        async submitResponse() {
            const selected = document.querySelector('input[name="answer"]:checked');

            if (!selected) {
                alert('Por favor, selecciona una respuesta');
                return;
            }

            const responseTime = this.getResponseTime();
            const answer = selected.value;

            // Deshabilitar botón mientras se envía
            const btnNext = document.getElementById('btnNext');
            btnNext.disabled = true;
            btnNext.textContent = 'Enviando...';

            try {
                const response = await fetch(this.config.submitUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.config.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        answer: answer,
                        response_time_ms: responseTime
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Remover el warning antes de navegar (solo modo normal)
                    if (this.beforeUnloadHandler) {
                        window.removeEventListener('beforeunload', this.beforeUnloadHandler);
                    }

                    // Navegar según la respuesta
                    if (data.next_item) {
                        window.location.href = data.next_item_url;
                    } else if (data.task_completed && data.next_task_url) {
                        window.location.href = data.next_task_url;
                    } else if (data.session_completed) {
                        window.location.href = data.completion_url;
                    } else if (data.demo_item_completed) {
                        // Demo de item individual: volver a la vista del item
                        window.location.href = data.completion_url;
                    } else if (data.battery_completed) {
                        // Demo de batería completa finalizado
                        window.location.href = data.completion_url;
                    } else if (data.demo_completed) {
                        // Demo de tarea completa
                        window.location.href = data.completion_url;
                    }
                } else {
                    alert(data.message || 'Error al guardar la respuesta. Por favor, intenta de nuevo.');
                    btnNext.disabled = false;
                    btnNext.textContent = 'Siguiente';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de conexión. Por favor, verifica tu conexión a internet.');
                btnNext.disabled = false;
                btnNext.textContent = 'Siguiente';
            }
        }

        preventBackNavigation() {
            history.pushState(null, null, location.href);
            window.onpopstate = function() {
                history.go(1);
                alert('No puedes retroceder durante la evaluación.');
            };
        }

        warnBeforeUnload() {
            this.beforeUnloadHandler = (e) => {
                e.preventDefault();
                e.returnValue = '¿Estás seguro de que quieres salir? Tu progreso se guardará.';
                return e.returnValue;
            };
            window.addEventListener('beforeunload', this.beforeUnloadHandler);
        }
    }

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        new SpatialPresentation(config);
    });
</script>
@endsection
