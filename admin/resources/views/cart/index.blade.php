<x-app-layout>

    <div class="min-h-screen flex justify-center items-start py-10 px-4">

        <div
            class="w-full max-w-5xl 
                bg-white dark:bg-white/10 
                shadow-lg rounded-xl p-6 
                border border-gray-200 dark:border-white/20">

            <h2 class="text-2xl font-bold mb-6 text-center 
                text-gray-800 dark:text-white">
                Your Cart 🛒
            </h2>

            <div id="cart-alert">
                <x-flash-message />
            </div>

            @if ($cartItems->count() > 0)

                <div class="overflow-x-auto">
                    <table class="w-full border rounded-lg overflow-hidden">

                        {{-- HEADER --}}
                        <thead>
                            <tr class="bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-white">
                                <th class="p-3">Product</th>
                                <th class="p-3">Price</th>
                                <th class="p-3">Qty</th>
                                <th class="p-3">Total</th>
                                <th class="p-3">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($cartItems as $item)
                                <tr id="row-{{ $item->product_id }}"
                                    class="text-center border-t 
                                        border-gray-200 dark:border-white/10 
                                        text-gray-800 dark:text-white">

                                    <!-- Product -->
                                    <td class="p-3">
                                        {{ $item->product->name }}
                                    </td>

                                    <!-- Price -->
                                    <td class="p-3">
                                        ₹{{ $item->unit_price }}
                                    </td>

                                    <!-- Quantity -->
                                    <td class="p-3">
                                        <div class="flex items-center justify-center gap-2">

                                            {{-- Decrease --}}
                                            <button onclick="updateQty('{{ $item->product_id }}', 'decrease')"
                                                class="px-2 py-1 rounded 
                                                    bg-gray-300 hover:bg-gray-400 
                                                    dark:bg-white/20">
                                                -
                                            </button>

                                            {{-- Quantity --}}
                                            <span id="qty-{{ $item->product_id }}" class="px-3 min-w-[30px]">
                                                {{ $item->quantity }}
                                            </span>

                                            {{-- Increase --}}
                                            <button onclick="updateQty('{{ $item->product_id }}', 'increase')"
                                                class="px-2 py-1 rounded 
                                                    bg-gray-300 hover:bg-gray-400 
                                                    dark:bg-white/20">
                                                +
                                            </button>

                                        </div>
                                    </td>

                                    <!-- Total -->
                                    <td id="total-{{ $item->product_id }}"
                                        class="p-3 font-semibold 
                                        text-green-600 dark:text-green-400">
                                        ₹{{ $item->total_price }}
                                    </td>

                                    <!-- Remove -->
                                    <td class="p-3">
                                        <button onclick="removeFromCart('{{ $item->product_id }}')"
                                            class="px-3 py-1 rounded transition
                                                bg-red-500 hover:bg-red-600 
                                                dark:bg-red-600 dark:hover:bg-red-700
                                                text-white">
                                            Remove
                                        </button>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                {{-- GRAND TOTAL --}}
                <div class="flex justify-end mt-6">
                    <div class="text-lg font-semibold 
                        text-gray-800 dark:text-white">
                        Grand Total:
                        <span id="grand-total" class="text-green-600 dark:text-green-400">
                            ₹{{ $grandTotal }}
                        </span>
                    </div>
                </div>

                {{-- ACTIONS: CLEAR CART & INVOICE --}}
                <div class="flex justify-end mt-4 space-x-4">
                    <button onclick="clearCart()"
                        class="px-5 py-2 rounded transition
                            bg-yellow-500 hover:bg-yellow-600 
                            dark:bg-yellow-600 dark:hover:bg-yellow-700
                            text-white">
                        Clear Cart
                    </button>
                    
                    <a href="{{ route('cart.invoice') }}"
                        class="px-5 py-2 rounded transition font-semibold
                            bg-blue-600 hover:bg-blue-700 
                            dark:bg-cyan-500 dark:hover:bg-cyan-600
                            text-white text-center">
                        📄 Generate Invoice
                    </a>

                    <a href="{{ route('checkout.index') }}"
                        class="px-8 py-2 rounded transition font-bold text-lg
                            bg-green-600 hover:bg-green-700 
                            dark:bg-green-500 dark:hover:bg-green-600
                            text-white text-center shadow-lg transform hover:-translate-y-1 active:scale-95">
                        💳 Checkout
                    </a>
                </div>
            @else
                <p class="text-center text-gray-500 dark:text-white/70">
                    Your cart is empty 😢
                </p>

            @endif
        </div>
    </div>

    <!-- jQuery CDN -->
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
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const summary = response.summary;
                        const item = summary.items.find(i => i.product_id == productId);
                        
                        if (item) {
                            $(`#qty-${productId}`).text(item.quantity);
                            $(`#total-${productId}`).text('₹' + item.total_price);
                            $('#grand-total').text('₹' + summary.grandTotal);
                            
                            // Inject the reusable flash component HTML
                            $('#cart-alert').html(response.flash_html);
                        } else if (action === 'decrease') {
                            window.location.reload();
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

        function removeFromCart(productId) {
            if (!confirm('Are you sure you want to remove this item?')) return;

            let url = cartRemoveUrl.replace(':id', productId);

            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        const summary = response.summary;
                        $(`#row-${productId}`).remove();
                        $('#grand-total').text('₹' + summary.grandTotal);

                        // Inject the reusable flash component HTML
                        $('#cart-alert').html(response.flash_html);

                        if (summary.items.length === 0) {
                            window.location.reload();
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
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.flash_html) {
                        $('#cart-alert').html(xhr.responseJSON.flash_html);
                    }
                }
            });
        }
    </script>
</x-app-layout>
