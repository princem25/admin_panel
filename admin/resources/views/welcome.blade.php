<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen 
bg-gradient-to-br from-[#0f2027] via-[#203a43] to-[#2c5364] text-white overflow-x-hidden">

<!-- 🌟 Glow Effects -->
<div class="absolute w-[500px] h-[500px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10"></div>
<div class="absolute w-[500px] h-[500px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10"></div>

<!-- ✅ Navbar (Glass Style) -->
<nav class="relative bg-white/10 backdrop-blur-xl border-b border-white/20 shadow-lg">
    <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">

        <h1 class="text-xl font-bold text-white">
            Product
        </h1>

        <div class="space-x-4 flex items-center">

            @auth
                <a href="#" class="text-white/80 hover:text-cyan-400 transition">
                    Dashboard
                </a>

                <span class="text-white/60">
                    {{ auth()->user()->name }}
                </span>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" 
                       class="text-white/80 hover:text-cyan-400 transition">
                        Login
                    </a>
                @endif

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" 
                       class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-1 rounded-lg transition">
                        Register
                    </a>
                @endif
            @endauth

        </div>

    </div>
</nav>

<!-- ✅ Main Content -->
<div class="relative p-6 max-w-6xl mx-auto">

    <!-- Header -->
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold mb-2">
            Product Management System
        </h1>
        <p class="text-white/70 text-lg">
            Easily manage products, cart, and invoices — all in one place
        </p>
    </div>

    <!-- Flow Section -->
    <div class="grid md:grid-cols-3 gap-8 text-center">

        <!-- Step 1 -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-xl shadow-lg hover:scale-105 transition">
            <div class="text-4xl mb-3">🛍</div>
            <h2 class="text-xl font-semibold mb-2">
                Browse Products
            </h2>
            <p class="text-white/70">
                View a list of available products with details like price and description.
            </p>
        </div>

        <!-- Step 2 -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-xl shadow-lg hover:scale-105 transition">
            <div class="text-4xl mb-3">🛒</div>
            <h2 class="text-xl font-semibold mb-2">
                Add to Cart
            </h2>
            <p class="text-white/70">
                Select products and add them to your cart for checkout.
            </p>
        </div>

        <!-- Step 3 -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-xl shadow-lg hover:scale-105 transition">
            <div class="text-4xl mb-3">🧾</div>
            <h2 class="text-xl font-semibold mb-2">
                Generate Invoice
            </h2>
            <p class="text-white/70">
                Create invoices instantly based on selected cart items.
            </p>
        </div>

    </div>

    <!-- Arrow -->
    <div class="hidden md:flex justify-center items-center mt-6 text-white/40 text-2xl">
        → → →
    </div>

    <!-- Overview Section -->
    <div class="mt-10 bg-white/10 backdrop-blur-xl border border-white/20 p-8 rounded-xl shadow-lg text-center">

        <h2 class="text-2xl font-bold mb-4">
            How It Works
        </h2>

        <p class="text-white/70 max-w-2xl mx-auto">
            This system allows users to manage products efficiently by browsing available items,
            adding them to a cart, and generating invoices — all within a simple and intuitive interface.
            Perfect for small businesses and admin panels.
        </p>

    </div>

</div>

</body>
</html>