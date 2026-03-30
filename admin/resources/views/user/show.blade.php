<x-app-layout>
    <div
        class="relative min-h-screen overflow-x-hidden 
        bg-white text-gray-900
        dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white">

        <!-- 🌟 Glow Effects (dark only) -->
        <div class="hidden dark:block absolute w-[400px] h-[400px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-20 left-10"></div>
        <div class="hidden dark:block absolute w-[400px] h-[400px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10"></div>

        <div class="relative w-[90%] md:w-[80%] mx-auto py-12">
            
            <!-- Back Button -->
            <a href="{{ route('user.products') }}" class="inline-flex items-center mb-6 text-blue-600 dark:text-cyan-400 hover:text-blue-800 dark:hover:text-cyan-300 transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Products
            </a>

            <!-- Product Container -->
            <div class="bg-white border border-gray-200 shadow-xl rounded-2xl overflow-hidden
                        dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-2xl">
                
                <div class="flex flex-col md:flex-row">
                    <!-- Left: Product Image -->
                    <div class="md:w-1/2 p-8 flex items-center justify-center bg-gray-50 dark:bg-white/5 relative h-80 md:h-auto">
                        <img src="{{ !empty($product->image) ? asset('storage/images/' . $product->image) : 'https://via.placeholder.com/500' }}"
                             alt="{{ $product->name ?? 'Product' }}" 
                             class="max-h-[350px] w-auto object-contain transition-transform duration-300 hover:scale-105 drop-shadow-lg">
                        
                        <!-- Category Badge -->
                        <span class="absolute top-6 left-6 px-4 py-1.5 bg-blue-100 text-blue-800 text-xs font-bold uppercase tracking-wider rounded-full dark:bg-cyan-900 dark:text-cyan-200 shadow-sm border border-blue-200 dark:border-cyan-800">
                            {{ $product->category?->name ?? 'Uncategorized' }}
                        </span>
                    </div>

                    <!-- Right: Product Details -->
                    <div class="md:w-1/2 p-8 lg:p-12 flex flex-col justify-center">
                        <h1 class="text-3xl md:text-5xl font-extrabold mb-4 text-gray-900 dark:text-white tracking-tight">
                            {{ $product->name ?? 'Unknown Product' }}
                        </h1>
                        
                        <div class="flex items-center gap-4 mb-8">
                            <p class="text-3xl font-black text-blue-600 dark:text-cyan-400 tracking-tight">
                                ₹{{ isset($product->price) ? number_format($product->price, 2) : '0.00' }}
                            </p>
                             
                        </div>

                        <div class="mb-10 flex-grow">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Description
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                                {{ $product->description ?? 'No detailed description available for this product at the moment.' }}
                            </p>
                        </div>

                        <div class="mt-auto pt-6 border-t border-gray-100 dark:border-white/10">
                            <!-- Add to cart -->
                            @if (isset($cartProductIds) && in_array($product->id ?? 0, $cartProductIds))
                                <button class="w-full py-4 px-6 text-lg font-bold rounded-xl bg-gray-200 text-gray-600 dark:bg-white/10 dark:text-gray-400 cursor-not-allowed shadow-inner transition-all flex justify-center items-center gap-2 border border-gray-300 dark:border-white/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Already in Cart
                                </button>
                            @else
                                <form action="{{ route('cart.add', $product->id ?? 0) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full py-4 px-6 text-lg font-bold rounded-xl text-white
                                                   bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600
                                                   dark:from-cyan-500 dark:to-blue-500 dark:hover:from-cyan-400 dark:hover:to-blue-400
                                                   shadow-[0_10px_20px_rgba(37,99,235,0.2)] hover:shadow-[0_15px_25px_rgba(37,99,235,0.3)] 
                                                   dark:shadow-[0_10px_20px_rgba(6,182,212,0.2)] dark:hover:shadow-[0_15px_25px_rgba(6,182,212,0.3)]
                                                   transform hover:-translate-y-1 transition-all duration-300 flex justify-center items-center gap-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        Add to Cart
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
