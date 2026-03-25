<x-app-layout>

    <div class="min-h-screen flex justify-center items-start py-10">

        <div class="w-full max-w-5xl bg-white dark:bg-white/10 
                shadow-lg rounded-xl p-6 border border-gray-200 dark:border-white/20">

            <h2 class="text-2xl font-bold mb-6 text-center">
                Your Cart 🛒
            </h2>

           <x-flash-message />  

            @if($cartItems->count() > 0)

                <div class="overflow-x-auto">
                    <table class="w-full border rounded-lg overflow-hidden">

                        <thead>
                            <tr class="bg-gray-100 dark:bg-white/10">
                                <th class="p-3">Product</th>
                                <th class="p-3">Price</th>
                                <th class="p-3">Qty</th>
                                <th class="p-3">Total</th>
                                <th class="p-3">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $grandTotal = 0; @endphp

                            @foreach($cartItems as $item)
                                @php
                                    $total = $item->product->price * $item->quantity;
                                    $grandTotal += $total;
                                @endphp

                                <tr class="text-center border-t">
                                    <td class="p-3">{{ $item->product->name }}</td>
                                    <td class="p-3">₹{{ $item->product->price }}</td>
                                    <td class="p-3">{{ $item->quantity }}</td>
                                    <td class="p-3 font-semibold">₹{{ $total }}</td>
                                    <td class="p-3">
                                        <form action="{{ route('cart.remove', $item->product_id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach


                        </tbody>
                    </table>
                </div>

                {{-- Clear Cart --}}
                <div class="flex justify-end mt-6">
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded">
                            Clear Cart
                        </button>
                    </form>
                </div>

            @else
                <p class="text-center text-gray-500 dark:text-white/70">
                    Your cart is empty 😢
                </p>
            @endif

        </div>

    </div>

</x-app-layout>