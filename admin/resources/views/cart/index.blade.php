<x-app-layout>

    <div class="min-h-screen bg-[#EAEDED] dark:bg-[#0f1111] py-6 px-4">
        <div class="max-w-7xl mx-auto">

            {{-- Page Title --}}
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                Shopping Cart
            </h1>

            {{-- Flash Messages --}}
            <div id="cart-alert" class="mb-4">

                {{-- Flash Message handled globally --}}

            </div>

            @if ($cartItems->count() > 0)

                <div class="flex flex-col lg:flex-row gap-4 items-start">

                    {{-- ──────────────── LEFT: Cart Items ──────────────── --}}
                    <div class="flex-1 min-w-0">

                        {{-- Items Card --}}
                        <div class="bg-white dark:bg-[#1a1a1a] rounded-lg shadow-sm border border-gray-200 dark:border-white/10 p-6">

                            {{-- Header Row --}}
                            <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-white/10 mb-2">
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                                    {{ $cartItems->count() }} {{ Str::plural('item', $cartItems->count()) }} in Cart
                                </h2>
                                <span class="text-sm text-gray-500 dark:text-white/50">Price</span>
                            </div>

                            {{-- Items List --}}
                            <div id="cart-items-wrapper">
                                @foreach ($cartItems as $item)
                                    <div id="row-{{ $item->product_id }}"
                                        class="flex gap-4 py-5 border-b border-gray-100 dark:border-white/10 last:border-0">

                                        <div class="flex-shrink-0">
                                            @if($item->product->image)
                                                <img src="{{ Storage::url('images/' . $item->product->image) }}"
                                                     alt="{{ $item->product->name }}"
                                                     class="w-28 h-28 object-cover rounded-md border border-gray-200 dark:border-white/10"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="hidden w-28 h-28 bg-gray-100 dark:bg-white/10 rounded-md items-center justify-center text-3xl">
                                                    📦
                                                </div>
                                            @else
                                                <div class="w-28 h-28 bg-gray-100 dark:bg-white/10 rounded-md flex items-center justify-center text-3xl">
                                                    📦
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Product Details --}}
                                        <div class="flex-1 flex flex-col justify-between">
                                            <div>
                                                {{-- Name --}}
                                                <h3 class="text-base font-semibold text-gray-900 dark:text-white leading-snug">
                                                    {{ $item->product->name }}
                                                </h3>

                                                {{-- Category --}}
                                                @if($item->product->category)
                                                    <p class="text-xs text-gray-500 dark:text-white/40 mt-0.5">
                                                        {{ $item->product->category->name }}
                                                    </p>
                                                @endif

                                                {{-- Discount Badge --}}
                                                @if($item->has_discount)
                                                    <span class="inline-block mt-1.5 px-2 py-0.5 text-xs font-bold rounded bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400">
                                                        SALE
                                                    </span>
                                                @endif

                                                {{-- In Stock --}}
                                                <p class="text-xs text-green-600 dark:text-green-400 mt-1 font-medium">
                                                    ✓ In Stock
                                                </p>

                                                {{-- Prime Mockup Badge --}}
                                                <div class="flex items-center gap-1.5 mt-2">
                                                    <span class="text-[10px] font-black italic bg-blue-500 text-white px-1 py-0.5 rounded-sm tracking-tighter shadow-sm leading-none">prime</span>
                                                    <span class="text-[10px] text-gray-500 dark:text-white/40">FREE One-Day</span>
                                                </div>
                                            </div>

                                            {{-- Bottom: Qty Controls + Remove --}}
                                            <div class="flex items-center gap-4 mt-3">

                                                {{-- Qty Selector --}}
                                                <div class="flex items-center border border-gray-300 dark:border-white/20 rounded-lg overflow-hidden bg-gray-50 dark:bg-white/5">
                                                    <button onclick="updateQty('{{ $item->product_id }}', 'decrease')"
                                                        class="w-8 h-8 flex items-center justify-center text-lg font-bold text-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-white/10 transition">
                                                        −
                                                    </button>
                                                    <span id="qty-{{ $item->product_id }}"
                                                        class="px-3 text-sm font-semibold text-gray-900 dark:text-white min-w-[36px] text-center border-x border-gray-300 dark:border-white/20 h-8 flex items-center justify-center">
                                                        {{ $item->quantity }}
                                                    </span>
                                                    <button onclick="updateQty('{{ $item->product_id }}', 'increase')"
                                                        class="w-8 h-8 flex items-center justify-center text-lg font-bold text-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-white/10 transition">
                                                        +
                                                    </button>
                                                </div>

                                                {{-- Divider --}}
                                                <span class="text-gray-300 dark:text-white/20">|</span>

                                                {{-- Remove --}}
                                                <button onclick="removeFromCart('{{ $item->product_id }}')"
                                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Price Column --}}
                                        <div class="flex-shrink-0 text-right min-w-[90px]">
                                            <p id="total-{{ $item->product_id }}"
                                               class="text-base font-bold text-gray-900 dark:text-white">
                                                ₹{{ number_format($item->total_price, 2) }}
                                            </p>
                                            @if($item->has_discount)
                                                <p class="text-xs text-gray-400 line-through mt-0.5">
                                                    ₹{{ number_format($item->product->price * $item->quantity, 2) }}
                                                </p>
                                                <p class="text-xs text-red-500 font-semibold">
                                                    Save ₹{{ number_format($item->savings, 2) }}
                                                </p>
                                            @endif
                                        </div>

                                    </div>
                                @endforeach
                            </div>

                            {{-- Subtotal at Bottom of List --}}
                            <div class="mt-4 text-right text-sm text-gray-600 dark:text-white/60">
                                Subtotal ({{ $cartItems->count() }} {{ Str::plural('item', $cartItems->count()) }}):
                                <span class="text-lg font-bold text-gray-900 dark:text-white ml-1">
                                    ₹<span id="subtotal-footer">{{ number_format($grandTotal, 2) }}</span>
                                </span>
                            </div>
                        </div>

                        {{-- Clear Cart --}}
                        <div class="mt-3 flex justify-end">
                            <button onclick="clearCart()"
                                class="text-sm text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 hover:underline transition">
                                🗑️ Clear entire cart
                            </button>
                        </div>

                    </div>

                    {{-- ──────────────── RIGHT: Order Summary Panel ──────────────── --}}
                    <div class="w-full lg:w-80 flex-shrink-0">
                        <div class="bg-white dark:bg-[#1a1a1a] rounded-lg shadow-sm border border-gray-200 dark:border-white/10 p-5 sticky top-6">

                            {{-- Free Shipping Progress --}}
                            <div class="mb-4">
                                <div class="flex items-center gap-2 text-xs text-green-600 dark:text-green-400 font-medium">
                                    <span class="w-5 h-5 flex items-center justify-center bg-green-100 dark:bg-green-500/20 rounded-full">✓</span>
                                    <span>Part of your order qualifies for FREE Shipping.</span>
                                </div>
                                @php
                                    $freeShippingThreshold = 5000;
                                    $progress = min(($grandTotal / $freeShippingThreshold) * 100, 100);
                                @endphp
                                <div class="mt-2 h-2 w-full bg-gray-100 dark:bg-white/5 rounded-full overflow-hidden border border-gray-200 dark:border-white/10">
                                    <div class="h-full bg-green-500 transition-all duration-1000" style="width: {{ $progress }}%"></div>
                                </div>
                                <p class="text-[11px] text-gray-500 dark:text-white/40 mt-1.5">
                                    @if($grandTotal >= $freeShippingThreshold)
                                        Your order qualifies for <span class="font-bold text-gray-700 dark:text-white/80">FREE Shipping</span>. Choose this option at checkout.
                                    @else
                                        Add ₹{{ number_format($freeShippingThreshold - $grandTotal, 2) }} more for <span class="font-bold">FREE Shipping</span>.
                                    @endif
                                </p>
                            </div>

                            {{-- Checkout Button --}}
                            <a href="{{ route('checkout.index') }}"
                                class="block w-full text-center py-2.5 px-4 rounded-full bg-[#FFD814] hover:bg-[#F7CA00] text-gray-900 font-medium text-sm shadow-sm transition active:scale-95 border border-[#FCD200]">
                                Proceed to Checkout
                            </a>

                            {{-- Divider --}}
                            <div class="border-t border-gray-100 dark:border-white/10 my-4"></div>

                            {{-- Order Breakdown --}}
                            <div class="space-y-2 text-sm text-gray-700 dark:text-white/70">
                                <div class="flex justify-between">
                                    <span>Items ({{ $cartItems->count() }}):</span>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        ₹<span id="order-subtotal">{{ number_format($grandTotal + $totalSavings, 2) }}</span>
                                    </span>
                                </div>

                                @if($totalSavings > 0)
                                    <div class="flex justify-between text-red-500 dark:text-red-400">
                                        <span>Discount:</span>
                                        <span class="font-semibold">− ₹<span id="order-savings">{{ number_format($totalSavings, 2) }}</span></span>
                                    </div>
                                @endif

                                <div class="flex justify-between">
                                    <span>Shipping:</span>
                                    <span class="text-green-600 dark:text-green-400 font-semibold">Free</span>
                                </div>
                            </div>

                            {{-- Divider --}}
                            <div class="border-t border-gray-100 dark:border-white/10 my-4"></div>

                            {{-- Grand Total --}}
                            <div class="flex justify-between items-center">
                                <span class="text-base font-bold text-gray-900 dark:text-white">Order Total:</span>
                                <span class="text-xl font-bold text-gray-900 dark:text-white">
                                    ₹<span id="grand-total">{{ number_format($grandTotal, 2) }}</span>
                                </span>
                            </div>

                            <div id="savings-message" class="{{ $totalSavings > 0 ? '' : 'hidden' }}">
                                <p class="text-xs text-green-600 dark:text-green-400 mt-2 font-medium">
                                    🎉 You save ₹<span>{{ number_format($totalSavings, 2) }}</span> on this order!
                                </p>
                            </div>

                            {{-- Divider --}}
                            <div class="border-t border-gray-100 dark:border-white/10 my-4"></div>

                            {{-- Continue Shopping --}}
                            <a href="{{ route('user.products') }}"
                                class="block text-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                ← Continue Shopping
                            </a>

                        </div>
                    </div>

                </div>

                {{-- ──────────────── BOTTOM: Recently Viewed Items (Amazon Style) ──────────────── --}}
                @if($recentProducts->count() > 0)
                    <div class="mt-12 border-t border-gray-200 dark:border-white/10 pt-8">
                        <div class="flex justify-between items-end mb-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Your Browsing History</h2>
                            <a href="{{ route('user.products') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">View all</a>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                            @foreach ($recentProducts as $recent)
                                <a href="{{ route('user.products.show', $recent->id) }}" 
                                   class="bg-white dark:bg-[#1a1a1a] rounded px-4 py-5 hover:shadow-lg transition border border-transparent hover:border-gray-100 dark:hover:border-white/10 group">
                                    <div class="w-full aspect-square bg-gray-50 dark:bg-white/5 rounded flex items-center justify-center mb-3 overflow-hidden">
                                        @if($recent->image)
                                            <div class="relative w-full h-full">
                                                <img src="{{ Storage::url('images/' . $recent->image) }}" 
                                                     class="w-full h-full object-cover rounded shadow-sm group-hover:scale-105 transition duration-300"
                                                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden'); this.nextElementSibling.classList.add('flex');">
                                                <div class="hidden w-full h-full bg-gray-50 dark:bg-white/5 items-center justify-center">
                                                    <span class="text-3xl">📦</span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-3xl">📦</span>
                                        @endif
                                    </div>
                                    <h4 class="text-sm font-medium text-blue-600 dark:text-blue-400 group-hover:text-blue-800 dark:group-hover:text-blue-300 group-hover:underline line-clamp-2 leading-snug">
                                        {{ $recent->name }}
                                    </h4>
                                    <div class="mt-1 flex items-center gap-1">
                                        <div class="flex text-yellow-400 text-xs">★★★★★</div>
                                        <span class="text-[10px] text-gray-400">(4.8)</span>
                                    </div>
                                    <p class="mt-1 text-base font-bold text-gray-900 dark:text-white">
                                        ₹{{ number_format($recent->discount_price > 0 ? $recent->discount_price : $recent->price, 2) }}
                                    </p>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <span class="text-[10px] font-black italic bg-blue-500 text-white px-1 py-0.5 rounded-sm tracking-tighter leading-none shadow-sm">prime</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            @else

                {{-- Empty Cart --}}
                <div class="bg-white dark:bg-[#1a1a1a] rounded-lg shadow-sm border border-gray-200 dark:border-white/10 p-16 flex flex-col items-center text-center">
                    <div class="text-7xl mb-4">🛒</div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Your Cart is Empty</h2>
                    <p class="text-gray-500 dark:text-white/50 mb-6 text-sm">
                        Looks like you haven't added anything yet.
                    </p>
                    <a href="{{ route('user.products') }}"
                        class="px-6 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold rounded-full transition text-sm shadow">
                        Start Shopping
                    </a>
                </div>

            @endif

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        const cartIncreaseUrl = "{{ route('cart.increase', ':id') }}";
        const cartDecreaseUrl = "{{ route('cart.decrease', ':id') }}";
        const cartRemoveUrl   = "{{ route('cart.remove', ':id') }}";
        const cartClearUrl    = "{{ route('cart.clear') }}";

        function updateQty(productId, action) {
            let url = action === 'increase' ? cartIncreaseUrl : cartDecreaseUrl;
            url = url.replace(':id', productId);

            $.ajax({
                url: url,
                type: 'PATCH',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        const summary = response.summary;
                        const item = summary.items.find(i => i.product_id == productId);

                        if (item) {
                            $(`#qty-${productId}`).text(item.quantity);
                            $(`#total-${productId}`).text('₹' + parseFloat(item.total_price).toFixed(2));
                            updateTotals(summary);
                        } else if (action === 'decrease') {
                            window.location.reload();
                        }

                        $('#cart-alert').html(response.flash_html);
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.flash_html) {
                        $('#cart-alert').html(xhr.responseJSON.flash_html);
                    }
                }
            });
        }

        function removeFromCart(productId) {
            if (!confirm('Remove this item from your cart?')) return;

            let url = cartRemoveUrl.replace(':id', productId);

            $.ajax({
                url: url,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        $(`#row-${productId}`).fadeOut(300, function() {
                            $(this).remove();
                        });
                        updateTotals(response.summary);
                        $('#cart-alert').html(response.flash_html);

                        if (response.summary.items.length === 0) {
                            setTimeout(() => window.location.reload(), 400);
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.flash_html) {
                        $('#cart-alert').html(xhr.responseJSON.flash_html);
                    }
                }
            });
        }

        function clearCart() {
            if (!confirm('Are you sure you want to clear the entire cart?')) return;

            $.ajax({
                url: cartClearUrl,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) window.location.reload();
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.flash_html) {
                        $('#cart-alert').html(xhr.responseJSON.flash_html);
                    }
                }
            });
        }

        function updateTotals(summary) {
            const grandTotal = parseFloat(summary.grandTotal || 0);
            const savings = parseFloat(summary.totalSavings || 0);
            const subtotal = grandTotal + savings;

            $('#grand-total').text(grandTotal.toFixed(2));
            $('#subtotal-footer').text(grandTotal.toFixed(2));
            $('#order-subtotal').text(subtotal.toFixed(2));
            $('#order-savings').text(savings.toFixed(2));

            // Show/hide discount row based on savings
            const discountRow = $('#order-savings').closest('.flex');
            if (savings > 0) {
                discountRow.show();
                $('#savings-message').show().find('span').text(savings.toFixed(2));
            } else {
                discountRow.hide();
                $('#savings-message').hide();
            }
        }
    </script>

</x-app-layout>
