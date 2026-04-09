<x-app-layout>
    <div
        class="relative min-h-screen overflow-x-hidden 
        bg-white text-gray-900
        dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white">

        <!-- 🌟 Glow Effects (dark only) -->
        <div
            class="hidden dark:block absolute w-[400px] h-[400px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-20 left-10">
        </div>
        <div
            class="hidden dark:block absolute w-[400px] h-[400px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10">
        </div>

        <div class="relative w-[90%] md:w-[80%] mx-auto py-12">

            <!-- Back Button -->
            <a href="{{ route('user.products') }}"
                class="inline-flex items-center mb-6 text-blue-600 dark:text-cyan-400 hover:text-blue-800 dark:hover:text-cyan-300 transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Products
            </a>

            <!-- Product Container -->
            <div
                class="bg-white border border-gray-200 shadow-xl rounded-2xl overflow-hidden
                        dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-2xl">

                <div class="flex flex-col md:flex-row">
                    <!-- Left: Product Image -->
                    <div
                        class="md:w-1/2 p-8 flex items-center justify-center bg-gray-50 dark:bg-white/5 relative h-80 md:h-auto">
                        <img src="{{ !empty($product->image) ? Storage::url('images/' . $product->image) : 'https://via.placeholder.com/500' }}"
                            alt="{{ $product->name ?? 'Product' }}"
                            class="max-h-[350px] w-auto object-contain transition-transform duration-300 hover:scale-105 drop-shadow-lg">

                        <!-- Category Badge -->
                        <span
                            class="absolute top-6 left-6 px-4 py-1.5 bg-blue-100 text-blue-800 text-xs font-bold uppercase tracking-wider rounded-full dark:bg-cyan-900 dark:text-cyan-200 shadow-sm border border-blue-200 dark:border-cyan-800">
                            {{ $product->category?->name ?? 'Uncategorized' }}
                        </span>
                    </div>

                    <!-- Right: Product Details -->
                    <div class="md:w-1/2 p-8 lg:p-12 flex flex-col justify-center">
                        <h1 class="text-3xl md:text-5xl font-extrabold mb-4 text-gray-900 dark:text-white tracking-tight">
                            {{ $product->name ?? 'Unknown Product' }}
                        </h1>

                        {{-- Price + Discount --}}
                        <div class="flex items-end gap-4 mb-3 flex-wrap">
                            @if ($product->discount_price && $product->discount_price < $product->price)
                                <div>
                                    <p class="text-3xl font-black text-green-600 dark:text-green-400 tracking-tight">
                                        ₹{{ number_format($product->discount_price, 2) }}
                                    </p>
                                    <p class="text-sm line-through text-gray-400 mt-0.5">
                                        ₹{{ number_format($product->price, 2) }}
                                    </p>
                                </div>
                                <span
                                    class="mb-1 px-3 py-1 bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400 text-sm font-bold rounded-full">
                                    {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}%
                                    OFF
                                    — You save ₹{{ number_format($product->price - $product->discount_price, 2) }}
                                </span>
                            @else
                                <p class="text-3xl font-black text-blue-600 dark:text-cyan-400 tracking-tight">
                                    ₹{{ number_format($product->price, 2) }}
                                </p>
                            @endif
                        </div>

                        {{-- Real-time Stock UI Container --}}
                        <div id="product-stock-wrapper-{{ $product->id }}">
                            @if (isset($product->stock))
                                <div id="out-of-stock-alert-{{ $product->id }}"
                                    class="mb-6 px-4 py-2 bg-red-100 border border-red-300 text-red-700
                                            dark:bg-red-500/10 dark:border-red-500/30 dark:text-red-400
                                            rounded-lg text-sm font-semibold {{ $product->stock == 0 ? 'flex' : 'hidden' }} items-center gap-2">
                                    🚫 This product is currently <strong>out of stock</strong>.
                                </div>

                                <div id="low-stock-alert-{{ $product->id }}"
                                    class="mb-6 px-4 py-2 bg-orange-50 border border-orange-300 text-orange-700
                                            dark:bg-orange-500/10 dark:border-orange-500/30 dark:text-orange-400
                                            rounded-lg text-sm font-semibold {{ $product->stock > 0 && $product->stock <= 5 ? 'flex' : 'hidden' }} items-center gap-2 animate-pulse">
                                    🔥 Hurry! Only <strong class="stock-count">{{ $product->stock }}</strong> left in
                                    stock!
                                </div>
                            @endif
                        </div>

                        <div class="mb-10 flex-grow">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Description
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg">
                                {{ $product->description ?? 'No detailed description available for this product at the moment.' }}
                            </p>
                        </div>

                        <div id="purchase-action-container-{{ $product->id }}">
                            @if (isset($product->stock) && $product->stock == 0)
                                <button id="add-to-cart-btn-{{ $product->id }}" disabled
                                    class="w-full py-4 px-6 text-lg font-bold rounded-xl
                                           bg-gray-200 text-gray-500 dark:bg-white/10 dark:text-gray-500
                                           cursor-not-allowed flex justify-center items-center gap-2 border border-gray-300 dark:border-white/20">
                                    🚫 Out of Stock
                                </button>
                            @elseif(isset($cartProductIds) && in_array($product->id ?? 0, $cartProductIds))
                                <button id="add-to-cart-btn-{{ $product->id }}"
                                    class="w-full py-4 px-6 text-lg font-bold rounded-xl bg-gray-200 text-gray-600 dark:bg-white/10 dark:text-gray-400 cursor-not-allowed shadow-inner transition-all flex justify-center items-center gap-2 border border-gray-300 dark:border-white/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Already in Cart
                                </button>
                            @else
                                <form id="add-to-cart-form-{{ $product->id }}"
                                    action="{{ route('cart.add', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" id="add-to-cart-btn-{{ $product->id }}"
                                        class="w-full py-4 px-6 text-lg font-bold rounded-xl text-white
                                                   bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600
                                                   dark:from-cyan-500 dark:to-blue-500 dark:hover:from-cyan-400 dark:hover:to-blue-400
                                                   shadow-[0_10px_20px_rgba(37,99,235,0.2)] hover:shadow-[0_15px_25px_rgba(37,99,235,0.3)]
                                                   dark:shadow-[0_10px_20px_rgba(6,182,212,0.2)] dark:hover:shadow-[0_15px_25px_rgba(6,182,212,0.3)]
                                                   transform hover:-translate-y-1 transition-all duration-300 flex justify-center items-center gap-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                        Add to Cart
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recently Viewed Products --}}
            @if (isset($recentProducts) && $recentProducts->count() > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-500 dark:text-cyan-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Recently Viewed
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                        @foreach ($recentProducts as $recentProduct)
                            <a href="{{ route('user.products.show', $recentProduct) }}"
                                class="group block bg-white dark:bg-white/10 border border-gray-200 dark:border-white/20
                              rounded-xl overflow-hidden shadow hover:shadow-lg dark:shadow-none
                              transition-all duration-300 hover:-translate-y-1">
                                <div
                                    class="h-36 bg-gray-50 dark:bg-white/5 flex items-center justify-center overflow-hidden">
                                    <img src="{{ !empty($recentProduct->image) ? Storage::url('images/' . $recentProduct->image) : 'https://via.placeholder.com/200' }}"
                                        alt="{{ $recentProduct->name }}"
                                        class="h-32 w-auto object-contain group-hover:scale-105 transition-transform duration-300">
                                </div>
                                <div class="p-4">
                                    <p class="font-semibold text-gray-800 dark:text-white text-sm truncate">
                                        {{ $recentProduct->name }}</p>
                                    <p class="text-blue-600 dark:text-cyan-400 font-bold text-sm mt-1">
                                        ₹{{ number_format($recentProduct->price, 2) }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
    @push('scripts')
        <script type="module">
            document.addEventListener('DOMContentLoaded', () => {
                const productId = {{ $product->id }};

                const els = {
                    outOfStock: document.getElementById(`out-of-stock-alert-${productId}`),
                    lowStock: document.getElementById(`low-stock-alert-${productId}`),
                    stockCounts: document.querySelectorAll(`#low-stock-alert-${productId} .stock-count`),
                    action: document.getElementById(`purchase-action-container-${productId}`),
                    wrapper: document.getElementById(`product-stock-wrapper-${productId}`)
                };

                const isAlreadyInCart = els.action.innerText.includes('Already in Cart');

                // 🔹 UI Helpers
                const show = el => el?.classList.replace('hidden', 'flex');
                const hide = el => el?.classList.replace('flex', 'hidden');

                // 🔹 Update Alerts
                function updateAlerts(stock) {
                    if (stock === 0) {
                        show(els.outOfStock);
                        hide(els.lowStock);
                    } else if (stock <= 5) {
                        hide(els.outOfStock);
                        show(els.lowStock);
                        els.stockCounts.forEach(el => el.textContent = stock);
                    } else {
                        hide(els.outOfStock);
                        hide(els.lowStock);
                    }
                }

                // 🔹 Update Button
                function updateButton(stock) {
                    if (isAlreadyInCart) return;

                    const btn = document.getElementById(`add-to-cart-btn-${productId}`);
                    if (!btn) return;

                    if (stock === 0) {
                        btn.disabled = true;
                        btn.textContent = '🚫 Out of Stock';
                        btn.classList.add('opacity-50', 'cursor-not-allowed');
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = '<svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>Add to Cart';
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                }

                // 🔹 Animation
                function animate() {
                    els.wrapper.classList.add('scale-105');
                    setTimeout(() => els.wrapper.classList.remove('scale-105'), 200);
                }

                // 🔹 Listen to event
                window.Echo.channel(`product.${productId}`)
                    .listen('.stock.updated', ({
                        newStock
                    }) => {
                        updateAlerts(newStock);
                        updateButton(newStock);
                        
                    });
            });
        </script>
    @endpush
</x-app-layout>
