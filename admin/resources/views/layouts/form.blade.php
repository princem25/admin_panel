<!DOCTYPE html>
<html lang="en" class="dark"> <!-- remove "dark" for white theme -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Layout</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body
    class="min-h-screen flex items-center justify-center relative overflow-y-auto 

bg-white text-gray-900
dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white">

    <!-- 🌟 Glow Effects (only dark mode) -->
    <div
        class="hidden dark:block absolute w-[400px] h-[400px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10">
    </div>
    <div
        class="hidden dark:block absolute w-[400px] h-[400px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10">
    </div>

    <div class="relative w-full max-w-md px-4">

        <!-- 🧊 Card Container -->
        <div
            class="rounded-2xl p-6 shadow-lg

        bg-white border border-gray-200
        dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20">

            {{-- Page Content --}}
            @yield('form')

        </div>

    </div>

</body>

</html>
