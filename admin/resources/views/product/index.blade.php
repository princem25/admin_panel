<x-app-layout>

    {{-- 🔔 Flash Message --}}
    <x-flash-message />

    <!-- 🌌 Background -->
    <div
        class="relative min-h-screen overflow-x-hidden bg-white text-gray-900 dark:bg-gradient-to-br 
        dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white">

        <!-- 🌟 Glow Effects (dark only) -->
        <div
            class="hidden dark:block absolute w-[500px] h-[500px] bg-cyan-400 opacity-20 blur-3xl 
            rounded-full top-10 left-10">
        </div>
        <div
            class="hidden dark:block absolute w-[500px] h-[500px] bg-blue-500 opacity-20 blur-3xl rounded-full
             bottom-10 right-10">
        </div>

        <div class="relative max-w-7xl mx-auto p-6">

            {{-- 🔝 Header --}}
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">

                <div>
                    <h2 class="text-2xl font-semibold">
                        {{ $greeting }}
                    </h2>

                    <p class="text-sm text-gray-600 dark:text-white/70">
                        Total Products: {{ $total_products }}
                    </p>

                    @if ($current_logged_user)
                        <p class="text-xs text-gray-500 dark:text-white/50">
                            Welcome, {{ $current_logged_user->name }}
                        </p>
                    @endif

                </div>

            </div>

            <!-- 🔍 Search -->
            <div class="flex justify-between gap-4 flex-wrap">

                <form method="GET" action="{{ route('products.index') }}"
                    class="mb-8 flex flex-wrap gap-4 items-end 
                    bg-white border border-gray-200 shadow-md
                    dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 
                    p-5 rounded-xl w-full md:w-[80%] m-auto">

                    <!-- Name -->
                    <div>
                        <label class="text-sm text-gray-600 dark:text-white/70">Product Name</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="border p-2 rounded-lg w-44 bg-white border-gray-300 text-gray-900
                            dark:bg-white/10 dark:border-white/20 dark:text-white">
                    </div>

                    <!-- Category -->
                    <!-- Category -->
                    <div>
                        <label class="text-sm text-gray-600 dark:text-white/70">
                            Category
                        </label>

                        <select name="category" c
                            class="border p-2 rounded-lg w-44 bg-white border-gray-300 text-gray-900
                            dark:bg-white/10 dark:border-white/20 dark:text-white">

                            <option value="" class="text-gray-500 dark:text-gray-300">
                                All Categories
                            </option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}
                                    class="bg-white text-black dark:bg-gray-800 dark:text-white">

                                    {{ $category->name }}

                                </option>
                            @endforeach

                        </select>
                    </div>
                    <!-- Price -->
                    <div>
                        <label class="text-sm text-gray-600 dark:text-white/70">Max Price</label>
                        <input type="number" name="price" value="{{ request('price') }}"
                            class="border p-2 rounded-lg w-32 
                            bg-white border-gray-300 text-gray-900
                            dark:bg-white/10 dark:border-white/20 dark:text-white">
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 rounded-lg text-white
                            bg-blue-600 hover:bg-blue-700
                            dark:bg-cyan-500 dark:hover:bg-cyan-600">
                            Search
                        </button>

                        <a href="{{ route('products.index') }}"
                            class="px-4 py-2 rounded-lg 
                            border border-gray-300 hover:bg-gray-100
                            dark:border-white/20 dark:hover:bg-white/10">
                            Reset
                        </a>
                    </div>

                </form>
                <div class="flex flex-row gap-3 absolute right-10 top-0">
                    <a href="{{ route('products.create') }}">
                        <button
                            class="px-5 py-2 rounded-lg shadow mt-5 block text-white
                            bg-blue-600 hover:bg-blue-700
                            dark:bg-cyan-500 dark:hover:bg-cyan-600">
                            + Create Product
                        </button>
                    </a>
                     <a href="{{ route('admin.products.export') }}"
                    class="inline-block px-5 h-10 mt-5 py-2 bg-green-600 text-white font-medium rounded-lg shadow hover:bg-green-700 hover:shadow-md transition duration-200">
                    Download CSV
                </a>
                <!-- Create -->

                </div>
               

            </div>

            {{-- 📦 Product Grid --}}
            @if (count($products) > 0)

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

                    @foreach ($products as $product)
                        <div
                            class="rounded-xl p-3 transition
                        bg-white border border-gray-200 shadow hover:scale-105
                        dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-lg">

                            <x-product-card :product="$product" />

                        </div>
                    @endforeach

                </div>
            @else
                <div class="text-center mt-10 
                text-gray-500 dark:text-white/60">
                    No products found
                </div>
            @endif

            {{-- 🔢 Pagination --}}
            <div class="mt-8">
                {{ $products->links() }}
            </div>

        </div>
    </div>

</x-app-layout>
