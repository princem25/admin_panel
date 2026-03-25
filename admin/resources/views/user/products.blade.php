<x-app-layout>

    <div class="relative min-h-screen 
        bg-gradient-to-br from-[#0f2027] via-[#203a43] to-[#2c5364] 
        text-white overflow-x-hidden">

        <!-- 🌟 Glow Effects -->
        <div class="absolute w-[500px] h-[500px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10"></div>
        <div class="absolute w-[500px] h-[500px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10"></div>

        <div class="relative w-[85%] mx-auto py-10">

            <!-- Title -->
            <h2 class="text-3xl font-bold mb-8 text-cyan-400 ">
                🛍️ Browse Products
            </h2>

            <!-- 🔍 Filter Form -->
            <form method="GET" action="{{ route('user.products') }}"
                class="mb-8 flex flex-wrap gap-3  
                bg-white/10 backdrop-blur-xl border border-white/20 
                p-4 rounded-xl shadow-lg w-[65%]">
                <input type="text" name="search"
                    value="{{ request('search') }}"
                    placeholder="Search product..."
                    class="border border-white/30 bg-white/20 backdrop-blur px-3 py-2 rounded text-white placeholder-gray-300">

                <input type="text" name="category"
                    value="{{ request('category') }}"
                    placeholder="Category"
                    class="border border-white/30 bg-white/20 backdrop-blur px-3 py-2 rounded text-white placeholder-gray-300">

                <input type="number" name="price"
                    value="{{ request('price') }}"
                    placeholder="Max Price"
                    class="border border-white/30 bg-white/20 backdrop-blur px-3 py-2 rounded text-white placeholder-gray-300">

                <button class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg transition">
                    Filter
                </button>

                <a href="{{ route('user.products') }}"
                    class="bg-gray-400/30 px-4 py-2 rounded-lg text-white">
                    Reset
                </a>

            </form>

            <!-- Flash Message -->
            @if(session('success'))
                <div class="bg-green-300/20 backdrop-blur text-green-200 p-3 rounded mb-6 text-center border border-green-300/30">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Product Grid -->
            @if($products->isEmpty())
                <p class="text-gray-300 text-center">No products found.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">

                    @foreach($products as $product)
                        <div class="bg-white/10 backdrop-blur-xl border border-white/20 
                                    p-4 rounded-xl shadow-lg 
                                    hover:scale-105 hover:shadow-2xl 
                                    transition duration-300">

                            <!-- Image -->
                            <div class="h-40 mb-3 flex items-center justify-center overflow-hidden rounded-lg bg-white/10">
                                <img src="{{ $product->image ? asset('images/' . $product->image) : 'https://via.placeholder.com/150' }}"
                                    alt="{{ $product->name }}"
                                    class="h-full object-contain transition hover:scale-110">
                            </div>

                            <!-- Name -->
                            <h3 class="text-lg font-semibold">
                                {{ $product->name }}
                            </h3>

                            <!-- Category -->
                            <p class="text-white/70 text-sm">
                                {{ $product->category }}
                            </p>

                            <!-- Price -->
                            <p class="text-cyan-400 font-bold mt-2">
                                ₹{{ $product->price }}
                            </p>

                            <!-- Actions -->
                            <div class="mt-4 flex justify-between items-center">

                                <a href="{{ route('cart.add', $product->id) }}"
                                   class="bg-cyan-500 hover:bg-cyan-600 px-3 py-1 rounded text-sm">
                                    Add
                                </a>

                                <a href="#"
                                   class="text-white/60 hover:text-cyan-400 text-sm">
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