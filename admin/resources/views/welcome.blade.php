<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Admin') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Vite (IMPORTANT) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-sm bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">

        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">
            {{ config('app.name', 'Admin') }}
        </h1>

        <!-- Button -->
        @auth
            <a href="{{ auth()->user()->role === 'admin' 
                ? route('admin.dashboard') 
                : route('dashboard') }}"
               class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition">
                Go to Dashboard
            </a>
        @else
            @if (Route::has('login'))
                <a href="{{ route('login') }}"
                   class="block w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition">
                    Login
                </a>
            @endif

            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="block w-full mt-3 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-800 dark:text-white py-2 rounded-lg transition">
                    Register
                </a>
            @endif
        @endauth

    </div>

</body>
</html>