<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Layout</title>

    {{-- Use Vite (recommended) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine (optional if used) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="min-h-screen 
bg-gradient-to-br from-[#0f2027] via-[#203a43] to-[#2c5364] 
text-white flex items-center justify-center relative overflow-hidden">

    <!-- 🌟 Glow Effects -->
    <div class="absolute w-[400px] h-[400px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10"></div>
    <div class="absolute w-[400px] h-[400px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10"></div>

    <div class="relative w-full max-w-md px-4">

        <!-- 🧊 Glass Card Container -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 
        shadow-lg rounded-2xl p-6">

            {{-- Page Content --}}
            @yield('form')

        </div>

    </div>

</body>
</html>