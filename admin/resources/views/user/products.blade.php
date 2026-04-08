<x-app-layout>

    <div
        class="relative min-h-screen overflow-x-hidden 

    bg-white text-gray-900
    dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white">

        <!-- 🌟 Glow Effects (dark only) -->
        <div
            class="hidden dark:block absolute w-[500px] h-[500px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10">
        </div>
        <div
            class="hidden dark:block absolute w-[500px] h-[500px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10">
        </div>

        <div class="relative w-[90%] mx-auto py-10">

            <!-- Title -->
            <h2 class="text-3xl font-bold mb-8 
            text-blue-600 dark:text-cyan-400">
                🛍️ Browse Products
            </h2>

              <!-- 🔍 Filter Form -->

            <div class="flex justify-between gap-4 flex-wrap">

                <form method="GET" action="{{ route('user.products') }}"
                    class="mb-8 flex flex-wrap gap-4 items-end 

                    bg-white border border-gray-200 shadow-md
                    dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 
                    p-5 rounded-xl w-full md:w-[80%]">

                    <!-- Name -->
                    <div>
                        <label class="text-sm text-gray-600 dark:text-white/70">Product Name</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="border p-2 rounded-lg w-44 bg-white border-gray-300 text-gray-900
                            dark:bg-white/10 dark:border-white/20 dark:text-white">
                    </div>
 
                    <!-- Category -->
                    <div>
                        <label class="text-sm text-gray-600 dark:text-white/70">
                            Category
                        </label>

                        <select name="category"
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

                        <a href="{{ route('user.products') }}"
                            class="px-4 py-2 rounded-lg 
                            border border-gray-300 hover:bg-gray-100
                            dark:border-white/20 dark:hover:bg-white/10">
                            Reset
                        </a>
                    </div>

                </form>

            </div>
            <!-- Flash Message -->
            <x-flash-message />

            <!-- Product Grid -->
            @if ($products->isEmpty())
                <p class="text-gray-500 dark:text-gray-300 text-center">
                    No products found.
                </p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">

                    @foreach ($products as $product)
                        <div
                            class="p-4 rounded-xl transition duration-200 bg-white border border-gray-200 shadow-sm dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md hover:shadow-md dark:hover:shadow-lg">

                            <!-- Image -->
                            <div
                                class="h-40 mb-3 flex items-center justify-center overflow-hidden rounded-lg bg-gray-100 dark:bg-white/10">
                                <img src="{{ $product->image ? asset('storage/images/' . $product->image) : 'https://via.placeholder.com/150' }}"
                                    alt="{{ $product->name }}"
                                    class="h-full object-contain transition duration-200 hover:scale-105">
                            </div>

                            <!-- Name -->
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $product->name }}
                            </h3>

                            <!-- Category -->
                            <p
                                class="text-sm text-gray-600 dark:text-white/70">
                                {{ $product->category?->name ?? 'Uncategorized' }}
                            </p>

                            {{-- Price + Discount --}}
                            <div class="mt-2">
                                @if($product->discount_price && $product->discount_price < $product->price)
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-black text-green-600 dark:text-green-400 text-lg">
                                            ₹{{ number_format($product->discount_price, 2) }}
                                        </span>
                                        <span class="line-through text-gray-400 text-sm">
                                            ₹{{ number_format($product->price, 2) }}
                                        </span>
                                        <span class="text-xs font-bold bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400 px-2 py-0.5 rounded-full">
                                            {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                                        </span>
                                    </div>
                                @else
                                    <span class="font-bold text-blue-600 dark:text-cyan-400">
                                        ₹{{ number_format($product->price, 2) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Low Stock / Out of Stock Badge --}}
                            @if(isset($product->stock))
                                @if($product->stock == 0)
                                    <span class="inline-block mt-1 text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 px-2 py-0.5 rounded-full">
                                        🚫 Out of Stock
                                    </span>
                                @elseif($product->stock <= 5)
                                    <span class="inline-block mt-1 text-xs font-semibold bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400 px-2 py-0.5 rounded-full">
                                        🔥 Only {{ $product->stock }} left!
                                    </span>
                                @endif
                            @endif

                            {{-- Actions --}}
                            <div class="mt-4 flex justify-between items-center">

                                @if($product->stock == 0)
                                    <button disabled
                                        class="bg-gray-300 text-gray-500 px-3 py-1 rounded cursor-not-allowed text-sm">
                                        Out of Stock
                                    </button>
                                @elseif(in_array($product->id, $cartProductIds))
                                    <button class="bg-green-500 text-white px-3 py-1 rounded cursor-not-allowed text-sm">
                                        ✔ Added
                                    </button>
                                @else
                                    <form action="{{ route('cart.add', $product) }}" method="POST">
                                        @csrf
                                        <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm transition">
                                            Add to Cart
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('user.products.show', $product) }}"
                                    class="text-sm text-gray-500 hover:text-gray-700
                                           dark:text-white/60 dark:hover:text-cyan-400">
                                    View →
                                </a>

                            </div>

                        </div>
                    @endforeach

                </div>
            @endif

            {{-- 🔢 Pagination --}}
            <div class="mt-10">
                {{ $products->links() }}
            </div>

        </div>
    </div>

</x-app-layout>
