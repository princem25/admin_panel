<!DOCTYPE html>
<html lang="en" class="dark"> <!-- remove "dark" for white theme -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen bg-white text-gray-900 dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] 
            dark:to-[#2c5364] dark:text-white overflow-x-hidden">

    {{-- Global Flash Notifications --}}
    <x-flash-message />

    <!-- 🌟 Glow Effects (only dark mode) -->
    <div
        class="hidden dark:block absolute w-[500px] h-[500px] bg-cyan-400 opacity-20 blur-3xl rounded-full 
            top-10 left-10">
    </div>
    <div
        class="hidden dark:block absolute w-[500px] h-[500px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10">
    </div>

    <!-- ✅ Navbar -->
    <nav
        class="relative 
bg-white border-b border-gray-200 shadow
dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20">

        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">

            <h1 class="text-xl font-bold text-gray-800 dark:text-white">
                Product
            </h1>

            <div class="space-x-4 flex items-center">

                @auth
                   <a href="{{ $current_logged_user->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                        class="text-gray-600 hover:text-blue-600 dark:text-white/80 dark:hover:text-cyan-400 transition">
                        Dashboard
                    </a>

                    <span class="text-gray-500 dark:text-white/60">
                        {{ $current_logged_user->name }}
                    </span>
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                            class="text-gray-600 hover:text-blue-600 dark:text-white/80 dark:hover:text-cyan-400 transition">
                            Login
                        </a>
                    @endif

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded-lg transition
                              dark:bg-cyan-500 dark:hover:bg-cyan-600">
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
            <p class="text-gray-600 dark:text-white/70 text-lg">
                Easily manage products, cart, and invoices — all in one place
            </p>
        </div>

        <!-- 🛍️ Featured Products -->
        <div class="mb-16">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Featured Products</h2>
                <a href="{{ route('user.products') }}" class="text-blue-600 dark:text-cyan-400 hover:underline">View All →</a>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                @foreach ($featuredProducts as $product)
                    <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm hover:shadow-md transition dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20">
                        <div class="h-32 mb-3 bg-gray-100 dark:bg-white/5 rounded-lg overflow-hidden flex items-center justify-center">
                            @if($product->image)
                                <img src="{{ $product->image ? Storage::url('images/' . $product->image) : 'https://via.placeholder.com/150' }}" alt="{{ $product->name }}" class="h-full object-contain">
                            @else
                                <span class="text-4xl text-gray-300">📦</span>
                            @endif
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-white/60 mb-2">{{ $product->category?->name ?? 'Uncategorized' }}</p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-blue-600 dark:text-cyan-400">@currency($product->price)</span>
                            <a href="{{ route('user.products.show', $product) }}" class="text-xs bg-gray-100 hover:bg-gray-200 dark:bg-white/20 dark:hover:bg-white/30 px-2 py-1 rounded transition">Details</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Flow Section -->
        <div class="grid md:grid-cols-3 gap-8 text-center">

            <!-- Step 1 -->
            <div
                class="bg-white border border-gray-200 p-6 rounded-xl shadow hover:scale-105 transition
                dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-lg">
                <div class="text-4xl mb-3">🛍</div>
                <h2 class="text-xl font-semibold mb-2">
                    Browse Products
                </h2>
                <p class="text-gray-600 dark:text-white/70">
                    View a list of available products with details like price and description.
                </p>
            </div>

            <!-- Step 2 -->
            <div
                class="bg-white border border-gray-200 p-6 rounded-xl shadow hover:scale-105 transition
                    dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-lg">
                <div class="text-4xl mb-3">🛒</div>
                <h2 class="text-xl font-semibold mb-2">
                    Add to Cart
                </h2>
                <p class="text-gray-600 dark:text-white/70">
                    Select products and add them to your cart for checkout.
                </p>
            </div>

            <!-- Step 3 -->
            <div
                class="bg-white border border-gray-200 p-6 rounded-xl shadow hover:scale-105 transition
                    dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-lg">
                <div class="text-4xl mb-3">🧾</div>
                <h2 class="text-xl font-semibold mb-2">
                    Generate Invoice
                </h2>
                <p class="text-gray-600 dark:text-white/70">
                    Create invoices instantly based on selected cart items.
                </p>
            </div>

        </div>

        <!-- Arrow -->
        <div class="hidden md:flex justify-center items-center mt-6 text-gray-400 dark:text-white/40 text-2xl">
            → → →
        </div>

        <!-- Overview Section -->
        <div
            class="mt-10 bg-white border border-gray-200 p-8 rounded-xl shadow text-center
                dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-lg">

            <h2 class="text-2xl font-bold mb-4">
                How It Works
            </h2>

            <p class="text-gray-600 dark:text-white/70 max-w-2xl mx-auto">
                This system allows users to manage products efficiently by browsing available items,
                adding them to a cart, and generating invoices — all within a simple and intuitive interface.
            </p>

        </div>

    </div>

</body>

</html>
