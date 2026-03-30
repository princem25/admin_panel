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

            <x-flash-message />

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
                                <tr
                                    class="text-center border-t 
                                        border-gray-200 dark:border-white/10 
                                        text-gray-800 dark:text-white">

                                    <!-- Product -->
                                    <td class="p-3">
                                        {{ $item->product->name }}
                                    </td>

                                    <!-- Price -->
                                    <td class="p-3">
                                        ₹{{ $item->product->price }}
                                    </td>

                                    <!-- Quantity -->
                                    <td class="p-3">
                                        <div class="flex items-center justify-center gap-2">

                                            {{-- Decrease --}}
                                            <form action="{{ route('cart.decrease', $item->product_id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    class="px-2 py-1 rounded 
                                                        bg-gray-300 hover:bg-gray-400 
                                                        dark:bg-white/20">
                                                    -
                                                </button>
                                            </form>

                                            {{-- Quantity --}}
                                            <span class="px-3">
                                                {{ $item->quantity }}
                                            </span>

                                            {{-- Increase --}}
                                            <form action="{{ route('cart.increase', $item->product_id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    class="px-2 py-1 rounded 
                                                        bg-gray-300 hover:bg-gray-400 
                                                        dark:bg-white/20">
                                                    +
                                                </button>
                                            </form>

                                        </div>
                                    </td>

                                    <!-- Total -->
                                    <td
                                        class="p-3 font-semibold 
                                        text-green-600 dark:text-green-400">
                                        ₹{{ $item->total_price }}
                                    </td>

                                    <!-- Remove -->
                                    <td class="p-3">
                                        <form action="{{ route('cart.remove', $item->product_id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                class="px-3 py-1 rounded transition
                                                    bg-red-500 hover:bg-red-600 
                                                    dark:bg-red-600 dark:hover:bg-red-700
                                                    text-white">
                                                Remove
                                            </button>
                                        </form>
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
                        <span class="text-green-600 dark:text-green-400">
                            ₹{{ $grandTotal }}
                        </span>
                    </div>
                </div>

                {{-- ACTIONS: CLEAR CART & INVOICE --}}
                <div class="flex justify-end mt-4 space-x-4">
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button
                            class="px-5 py-2 rounded transition
                                bg-yellow-500 hover:bg-yellow-600 
                                dark:bg-yellow-600 dark:hover:bg-yellow-700
                                text-white">
                            Clear Cart
                        </button>
                    </form>

                    <a href="{{ route('cart.invoice') }}"
                        class="px-5 py-2 rounded transition font-semibold
                            bg-blue-600 hover:bg-blue-700 
                            dark:bg-cyan-500 dark:hover:bg-cyan-600
                            text-white text-center">
                        📄 Generate Invoice
                    </a>
                </div>
            @else
                <p class="text-center text-gray-500 dark:text-white/70">
                    Your cart is empty 😢
                </p>

            @endif
                
        </div>

    </div>

</x-app-layout>
