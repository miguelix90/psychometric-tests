<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso con Código - {{ $batteryCode->battery->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">

            <!-- Información del código -->
            <div class="mb-6 text-center">
                <div class="inline-block bg-indigo-100 rounded-lg px-4 py-2 mb-4">
                    <p class="text-xs text-indigo-600 font-semibold uppercase tracking-wide">Código de Batería</p>
                    <p class="text-2xl font-mono font-bold text-indigo-900">{{ $code }}</p>
                </div>

                <h1 class="text-2xl font-bold text-gray-800">
                    {{ $batteryCode->battery->name }}
                </h1>
                <p class="text-sm text-gray-600 mt-2">
                    {{ $batteryCode->institution->name }}
                </p>
            </div>

            <!-- Información de la batería -->
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Sobre esta batería</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>{{ $batteryCode->battery->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Título -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 text-center">
                    Ingresa tus datos
                </h2>
                <p class="text-sm text-gray-600 text-center mt-2">
                    Proporciona tus datos personales para acceder
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
            <form method="POST" action="{{ route('test.battery-code.validate', $code) }}">
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
                        Acceder a la Evaluación
                    </button>
                </div>
            </form>

            <!-- Información adicional -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    Ingresa los mismos datos que proporcionaste a tu profesor.
                </p>
            </div>

            <!-- Información de privacidad -->
            <div class="mt-6 p-4 bg-green-50 rounded-lg">
                <h3 class="text-sm font-semibold text-green-800 mb-2 flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Protección de Datos
                </h3>
                <p class="text-xs text-green-700">
                    Estos datos se usan únicamente para verificar tu identidad. No se almacenan en nuestros servidores, solo se genera un código único de identificación.
                </p>
            </div>

            <!-- Info del código -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <div>
                        <span class="font-semibold">Usos disponibles:</span>
                        {{ $batteryCode->max_uses - $batteryCode->current_uses }} / {{ $batteryCode->max_uses }}
                    </div>
                    <div>
                        <span class="font-semibold">Expira:</span>
                        {{ $batteryCode->expires_at->diffForHumans() }}
                    </div>
                </div>
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
