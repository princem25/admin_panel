<x-app-layout>

    <div class="relative min-h-screen overflow-x-hidden 

    bg-white text-gray-900
    dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white">

        <!-- 🌟 Glow Effects (dark only) -->
        <div class="hidden dark:block absolute w-[500px] h-[500px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10"></div>
        <div class="hidden dark:block absolute w-[500px] h-[500px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10"></div>

        <div class="relative w-[85%] mx-auto py-10">

            <!-- Title -->
            <h2 class="text-3xl font-bold mb-8 
            text-blue-600 dark:text-cyan-400">
                🛍️ Browse Products
            </h2>

            <!-- 🔍 Filter Form -->
            <form method="GET" action="{{ route('user.products') }}"
                class="mb-8 flex flex-wrap gap-3 p-4 rounded-xl shadow w-[65%]

                bg-white border border-gray-200
                dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20">

                <input type="text" name="search"
                    value="{{ request('search') }}"
                    placeholder="Search product..."
                    class="px-3 py-2 rounded 

                    bg-white border border-gray-300 text-gray-900 placeholder-gray-500
                    dark:bg-white/20 dark:border-white/30 dark:text-white dark:placeholder-gray-300">

                <input type="text" name="category"
                    value="{{ request('category') }}"
                    placeholder="Category"
                    class="px-3 py-2 rounded 

                    bg-white border border-gray-300 text-gray-900
                    dark:bg-white/20 dark:border-white/30 dark:text-white">

                <input type="number" name="price"
                    value="{{ request('price') }}"
                    placeholder="Max Price"
                    class="px-3 py-2 rounded 

                    bg-white border border-gray-300 text-gray-900
                    dark:bg-white/20 dark:border-white/30 dark:text-white">

                <button class="px-4 py-2 rounded-lg text-white

                bg-blue-600 hover:bg-blue-700
                dark:bg-cyan-500 dark:hover:bg-cyan-600">
                    Filter
                </button>

                <a href="{{ route('user.products') }}"
                    class="px-4 py-2 rounded-lg 

                    bg-gray-200 text-gray-700 hover:bg-gray-300
                    dark:bg-gray-400/30 dark:text-white">
                    Reset
                </a>

            </form>

            <!-- Flash Message -->
            @if(session('success'))
                <div class="p-3 rounded mb-6 text-center border

                bg-green-100 text-green-700 border-green-300
                dark:bg-green-300/20 dark:text-green-200 dark:border-green-300/30">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Product Grid -->
            @if($products->isEmpty())
                <p class="text-gray-500 dark:text-gray-300 text-center">
                    No products found.
                </p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">

                    @foreach($products as $product)
                        <div class="p-4 rounded-xl transition duration-200

                        bg-white border border-gray-200 shadow-sm
                        dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md

                        hover:shadow-md dark:hover:shadow-lg">

                            <!-- Image -->
                            <div class="h-40 mb-3 flex items-center justify-center overflow-hidden rounded-lg

                            bg-gray-100
                            dark:bg-white/10">
                                <img src="{{ $product->image ? asset('images/' . $product->image) : 'https://via.placeholder.com/150' }}"
                                    alt="{{ $product->name }}"
                                    class="h-full object-contain transition duration-200 hover:scale-105">
                            </div>

                            <!-- Name -->
                            <h3 class="text-lg font-semibold 
                            text-gray-900 dark:text-white">
                                {{ $product->name }}
                            </h3>

                            <!-- Category -->
                            <p class="text-sm 
                            text-gray-600 dark:text-white/70">
                                {{ $product->category }}
                            </p>

                            <!-- Price -->
                            <p class="font-bold mt-2 
                            text-blue-600 dark:text-cyan-400">
                                ₹{{ $product->price }}
                            </p>

                            <!-- Actions -->
                            <div class="mt-4 flex justify-between items-center">

                                <a href="{{ route('cart.add', $product->id) }}"
                                   class="px-3 py-1 rounded text-sm text-white

                                   bg-blue-600 hover:bg-blue-700
                                   dark:bg-cyan-500 dark:hover:bg-cyan-600">
                                    Add
                                </a>

                                <a href="#"
                                   class="text-sm 

                                   text-gray-500 hover:text-gray-700
                                   dark:text-white/60 dark:hover:text-cyan-400">
                                    View
                                </a>

                            </div>

                        </div>
                    @endforeach

                </div>
            @endif

        </div>
    </div>

</x-app-layout>