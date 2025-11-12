<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Evaluación Cognitiva')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 antialiased">

    <!-- Barra de progreso fija -->
    <div class="fixed top-0 left-0 right-0 bg-white shadow-sm z-50">
        @yield('progress-bar')
    </div>

    <!-- Contenido principal -->
    <main class="container mx-auto px-4 pt-20 pb-8">
        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
