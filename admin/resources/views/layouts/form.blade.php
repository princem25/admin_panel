<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Layout</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-2xl">
        
        {{-- Card Container --}}
        <div class="bg-white shadow-lg rounded-lg p-6">

            {{-- Page Content --}}
            @yield('form')

        </div>

    </div>

</body>
</html> 