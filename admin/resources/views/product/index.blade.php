{{-- 🔔 Flash Message --}}
@if(session('success') || session('error'))
    <div id="flash-message" class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50 
                max-w-sm w-full text-center p-3 rounded shadow-lg transition-opacity duration-500
                {{ session('success') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">

        {{ session('success') ?? session('error') }}
    </div>

    <script>
        setTimeout(() => {
            const el = document.getElementById('flash-message');
            if (el) {
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }
        }, 3000);
    </script>
@endif

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/js/app.js'])
</head>
<body class="min-h-screen 
bg-gradient-to-br from-[#0f2027] via-[#203a43] to-[#2c5364] text-white">

<!-- 🌟 Glow Effects -->
<div class="absolute w-[500px] h-[500px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10"></div>
<div class="absolute w-[500px] h-[500px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10"></div>

<div class="relative max-w-7xl mx-auto p-6">

    {{-- 🔝 Header --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">

        <div>
            <h2 class="text-2xl font-semibold">
                {{$greeting}}
            </h2>

            <p class="text-sm text-white/70">
                Total Products: {{ $total_products }}
            </p>

            @if($current_logged_user)
                <p class="text-xs text-white/50">
                    Welcome, {{ $current_logged_user->name }}
                </p>
            @endif
        </div>

        <a href="{{ route('products.create') }}">
            <button class="px-5 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg shadow transition">
                + Create Product
            </button>
        </a>
    </div>

    {{-- 🔍 Search Box --}}
    <form method="GET" action="{{ route('products.index') }}"
        class="mb-8 flex flex-wrap gap-4 items-end 
        bg-white/10 backdrop-blur-xl border border-white/20 
        p-5 rounded-xl shadow-md w-full md:w-[80%]">

        <div>
            <label class="text-sm text-white/70">Product Name</label>
            <input type="text" name="search" value="{{ request('search') }}"
                class="border border-white/20 bg-white/10 text-white p-2 rounded-lg w-44 focus:ring-2 focus:ring-cyan-400 outline-none">
        </div>

        <div>
            <label class="text-sm text-white/70">Category</label>
            <select name="category"
                class="border border-white/20 bg-white/10 text-white p-2 rounded-lg w-44 focus:ring-2 focus:ring-cyan-400 outline-none">
                <option value="">All</option>
                <option value="electronics" {{ request('category') == 'electronics' ? 'selected' : '' }}>Electronics</option>
                <option value="fashion" {{ request('category') == 'fashion' ? 'selected' : '' }}>Fashion</option>
                <option value="books" {{ request('category') == 'books' ? 'selected' : '' }}>Books</option>
            </select>
        </div>

        <div>
            <label class="text-sm text-white/70">Max Price</label>
            <input type="number" name="price" value="{{ request('price') }}"
                class="border border-white/20 bg-white/10 text-white p-2 rounded-lg w-32 focus:ring-2 focus:ring-cyan-400 outline-none">
        </div>

        <div class="flex gap-2">
            <button type="submit"
                class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition">
                Search
            </button>

            <a href="{{ route('products.index') }}"
                class="px-4 py-2 border border-white/20 rounded-lg hover:bg-white/10 transition">
                Reset
            </a>
        </div>
    </form>

    {{-- 📦 Product Grid --}}
    @if(count($products) > 0)

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            @foreach($products as $product)
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 
                rounded-xl shadow-lg hover:scale-105 transition p-3">

                    <x-product-card :product="$product" />

                </div>
            @endforeach

        </div>

    @else
        <div class="text-center text-white/60 mt-10">
            No products found
        </div>
    @endif

    {{-- 🔙 Back Button --}}
    <div class="mt-10 text-right">
        <a href="{{ route('admin.dashboard') }}"
            class="px-5 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition">
            ← Dashboard
        </a>
    </div>

</div>

</body>

</html>