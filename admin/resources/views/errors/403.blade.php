<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-yellow-600">403</h1>
        <p class="text-xl text-gray-600 mt-4">Access Denied! You do not have permission to view this page.</p>
        <a href="{{ url('/') }}" class="mt-6 inline-block bg-yellow-600 text-white px-6 py-3 rounded-lg hover:bg-yellow-700 transition">Go Back Home</a>
    </div>
</body>
</html>
