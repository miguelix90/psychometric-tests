<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Cuestionario - {{ $institution->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <!-- Logo o nombre de la institución -->
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ $institution->name }}
                </h1>
                <p class="text-sm text-gray-600 mt-2">
                    {{ $institution->type->label() }}
                </p>
            </div>

            <!-- Título -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 text-center">
                    Acceso al Cuestionario
                </h2>
                <p class="text-sm text-gray-600 text-center mt-2">
                    Ingresa tus datos personales para acceder
                </p>
            </div>

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

            <!-- Formulario -->
            <form method="POST" action="{{ route('participant.access.validate', $access_code) }}">
                @csrf

                <!-- Nombre -->
                <div class="mb-4">
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre
                    </label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('first_name') border-red-500 @enderror"
                        placeholder="Tu nombre"
                        required
                        autofocus
                    >
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Apellido -->
                <div class="mb-4">
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Apellido
                    </label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('last_name') border-red-500 @enderror"
                        placeholder="Tu apellido"
                        required
                    >
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de Nacimiento -->
                <div class="mb-4">
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Nacimiento
                    </label>
                    <input
                        type="date"
                        id="birth_date"
                        name="birth_date"
                        value="{{ old('birth_date') }}"
                        max="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('birth_date') border-red-500 @enderror"
                        required
                    >
                    @error('birth_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sexo -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Sexo
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input
                                type="radio"
                                name="sex"
                                value="M"
                                {{ old('sex') == 'M' ? 'checked' : '' }}
                                class="mr-2 focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                required
                            >
                            <span class="text-sm text-gray-700">Masculino</span>
                        </label>
                        <label class="flex items-center">
                            <input
                                type="radio"
                                name="sex"
                                value="F"
                                {{ old('sex') == 'F' ? 'checked' : '' }}
                                class="mr-2 focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                required
                            >
                            <span class="text-sm text-gray-700">Femenino</span>
                        </label>
                        <label class="flex items-center">
                            <input
                                type="radio"
                                name="sex"
                                value="O"
                                {{ old('sex') == 'O' ? 'checked' : '' }}
                                class="mr-2 focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                                required
                            >
                            <span class="text-sm text-gray-700">Otro</span>
                        </label>
                    </div>
                    @error('sex')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botón de acceso -->
                <div class="mt-6">
                    <button
                        type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Acceder al Cuestionario
                    </button>
                </div>
            </form>

            <!-- Información adicional -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    Ingresa los mismos datos que proporcionaste a tu profesor/evaluador.
                </p>
            </div>

            <!-- Información de privacidad -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-semibold text-blue-800 mb-2">
                    🔒 Protección de Datos
                </h3>
                <p class="text-xs text-blue-700">
                    Estos datos se usan únicamente para verificar tu identidad. No se almacenan en nuestros servidores, solo se genera un código único de identificación.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-500">
                Sistema de Evaluación Psicométrica Online
            </p>
        </div>
    </div>
</body>
</html>
