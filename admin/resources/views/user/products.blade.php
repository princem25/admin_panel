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

            <h2 class="text-3xl font-bold mb-8 text-blue-600 dark:text-cyan-400">
                🛍️ Browse Products
            </h2>

              <!-- 🔍 Filter -->
            <div class="flex justify-center mb-8">
                <form method="GET" action="{{ route('user.products') }}"
                    class="flex flex-wrap gap-4 items-end 
                    bg-white border border-gray-200 shadow-lg
                    dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 
                    p-4 rounded-xl w-full max-w-7xl">

                    <!-- 🚀 Sort By -->
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-1.5 ml-1">Sort By</label>
                        <select name="sort" onchange="this.form.submit()"
                            class="w-full h-10 py-1 text-sm border-gray-200 dark:border-white/10 rounded-lg bg-white dark:bg-[#2c3e50] text-gray-900 dark:text-gray-100 focus:ring-blue-500">
                            <option value="newest" class="text-black dark:text-white bg-white dark:bg-[#2c3e50]" {{ request('sort') == 'newest' ? 'selected' : '' }}>✨ Newest</option>
                            <option value="price_low" class="text-black dark:text-white bg-white dark:bg-[#2c3e50]" {{ request('sort') == 'price_low' ? 'selected' : '' }}>📉 Lowest Price</option>
                            <option value="price_high" class="text-black dark:text-white bg-white dark:bg-[#2c3e50]" {{ request('sort') == 'price_high' ? 'selected' : '' }}>📈 Highest Price</option>
                            <option value="popularity" class="text-black dark:text-white bg-white dark:bg-[#2c3e50]" {{ request('sort') == 'popularity' ? 'selected' : '' }}>🔥 Popularity</option>
                        </select>
                    </div>

                    <!-- 🔍 Search -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-1.5 ml-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full h-10 py-1 text-sm border-gray-200 dark:border-white/10 rounded-lg bg-white dark:bg-[#2c3e50] text-gray-900 dark:text-gray-100 focus:ring-blue-500"
                            placeholder="Product name...">
                    </div>

                    <!-- 🏷️ Category -->
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-1.5 ml-1">Category</label>
                        <select name="category" onchange="this.form.submit()"
                            class="w-full h-10 py-1 text-sm border-gray-200 dark:border-white/10 rounded-lg bg-white dark:bg-[#2c3e50] text-gray-900 dark:text-gray-100 focus:ring-blue-500">
                            <option value="" class="text-black dark:text-white bg-white dark:bg-[#2c3e50]">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" class="text-black dark:text-white bg-white dark:bg-[#2c3e50]" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 💰 Price Range -->
                    <div class="flex items-end gap-2">
                        <div class="w-24">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-1.5 ml-1">Min Price</label>
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                class="w-full h-10 py-1 text-sm border-gray-200 dark:border-white/10 rounded-lg bg-white dark:bg-[#2c3e50] text-gray-900 dark:text-gray-100 focus:ring-blue-500"
                                placeholder="₹ Min">
                        </div>
                        <div class="w-24">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-white/40 mb-1.5 ml-1">Max Price</label>
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                class="w-full h-10 py-1 text-sm border-gray-200 dark:border-white/10 rounded-lg bg-white dark:bg-[#2c3e50] text-gray-900 dark:text-gray-100 focus:ring-blue-500"
                                placeholder="₹ Max">
                        </div>
                    </div>

                    <!-- ✅ Quick Filters -->
                    <div class="flex items-center gap-4 pb-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()"
                                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-white/10 dark:border-white/20">
                            <span class="text-xs font-semibold text-gray-600 dark:text-white/70 group-hover:text-blue-500 transition">In Stock</span>
                        </label>
                        
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="on_sale" value="1" {{ request('on_sale') ? 'checked' : '' }} onchange="this.form.submit()"
                                class="w-4 h-4 rounded border-gray-300 text-red-600 focus:ring-red-500 dark:bg-white/10 dark:border-white/20">
                            <span class="text-xs font-semibold text-gray-600 dark:text-white/70 group-hover:text-red-500 transition">On Sale</span>
                        </label>
                    </div>

                    <!-- ⚡ Buttons -->
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-5 py-2.5 rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition active:scale-95 text-sm font-bold">
                            Filter
                        </button>
                        <a href="{{ route('user.products') }}"
                            class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-white/20 hover:bg-gray-100 dark:hover:bg-white/10 dark:text-white transition text-sm font-medium">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Flash Message -->
            <x-flash-message />

            <!-- 📊 Results Summary -->
            <div class="mb-6 flex justify-between items-center bg-gray-50/50 dark:bg-white/5 p-4 rounded-xl border border-gray-100 dark:border-white/10">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-300">
                    Showing <span class="text-blue-600 dark:text-cyan-400 font-bold">{{ $products->total() }}</span> products
                    @if(request('search'))
                        for <span class="italic text-gray-900 dark:text-white">"{{ request('search') }}"</span>
                    @endif
                    @if(request('min_price') || request('max_price'))
                        in range <span class="font-bold text-gray-900 dark:text-white">₹{{ request('min_price', 0) }} - ₹{{ request('max_price', '100000') }}</span>
                    @endif
                </div>

                <div class="flex gap-2">
                    @if(request('in_stock'))
                        <span class="px-2 py-1 bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400 text-[10px] font-bold uppercase rounded-md">In Stock</span>
                    @endif
                    @if(request('on_sale'))
                        <span class="px-2 py-1 bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 text-[10px] font-bold uppercase rounded-md">On Sale</span>
                    @endif
                </div>
            </div>

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
                                <img src="{{ $product->image ? Storage::url('images/' . $product->image) : 'https://via.placeholder.com/150' }}"
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
