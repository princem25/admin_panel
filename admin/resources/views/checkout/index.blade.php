<x-app-layout>
    <div class="min-h-screen py-12 px-4">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row gap-8">
            
            {{-- CHECKOUT FORM --}}
            <div class="flex-1">
                <div class="bg-white dark:bg-white/10 shadow-lg rounded-xl p-8 border border-gray-200 dark:border-white/20">
                    <h2 class="text-3xl font-extrabold mb-8 text-gray-800 dark:text-white flex items-center gap-3">
                        Shipping Information 🚚
                    </h2>

                    <form action="{{ route('checkout.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Full Name --}}
                            <div>
                                <label for="full_name" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                                <input type="text" name="full_name" id="full_name" 
                                    value="{{ old('full_name', $current_logged_user->name) }}" required
                                    class="w-full px-4 py-3 rounded-lg border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter your full name">
                                @error('full_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                                <input type="text" name="phone" id="phone" 
                                    value="{{ old('phone') }}" required
                                    class="w-full px-4 py-3 rounded-lg border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter your contact number">
                                @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Shipping Address --}}
                        <div>
                            <label for="shipping_address" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">Shipping Address</label>
                            <textarea name="shipping_address" id="shipping_address" rows="4" required
                                class="w-full px-4 py-3 rounded-lg border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter your full shipping address">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Payment Method with Alpine.js for Tailwind 3.1 compatibility --}}
                        <div class="space-y-4" x-data="{ method: '{{ old('payment_method', 'cod') }}' }">
                            <label class="block font-bold text-gray-700 dark:text-gray-300 mb-2">Select Payment Method 💳</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                {{-- COD --}}
                                <label 
                                    class="cursor-pointer relative flex items-center p-4 border-2 rounded-xl transition hover:bg-gray-50 dark:hover:bg-white/5"
                                    :class="method === 'cod' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-500/10' : 'border-gray-200 dark:border-white/10'">
                                    
                                    <input type="radio" name="payment_method" value="cod" class="hidden" x-model="method">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-blue-100 dark:bg-blue-500/20 rounded-lg text-blue-600 dark:text-blue-400">
                                            🚚
                                        </div>
                                        <span class="font-bold text-gray-800 dark:text-white">COD</span>
                                    </div>
                                    {{-- Custom indicator --}}
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 transition-colors"
                                         :class="method === 'cod' ? 'border-blue-500 bg-blue-500' : 'border-gray-300 dark:border-white/20'"></div>
                                </label>

                                {{-- UPI --}}
                                <label 
                                    class="cursor-pointer relative flex items-center p-4 border-2 rounded-xl transition hover:bg-gray-50 dark:hover:bg-white/5"
                                    :class="method === 'upi' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-500/10' : 'border-gray-200 dark:border-white/10'">
                                    
                                    <input type="radio" name="payment_method" value="upi" class="hidden" x-model="method">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-purple-100 dark:bg-purple-500/20 rounded-lg text-purple-600 dark:text-purple-400">
                                            📱
                                        </div>
                                        <span class="font-bold text-gray-800 dark:text-white">UPI</span>
                                    </div>
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 transition-colors"
                                         :class="method === 'upi' ? 'border-blue-500 bg-blue-500' : 'border-gray-300 dark:border-white/20'"></div>
                                </label>

                                {{-- CARD --}}
                                <label 
                                    class="cursor-pointer relative flex items-center p-4 border-2 rounded-xl transition hover:bg-gray-50 dark:hover:bg-white/5"
                                    :class="method === 'card' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-500/10' : 'border-gray-200 dark:border-white/10'">
                                    
                                    <input type="radio" name="payment_method" value="card" class="hidden" x-model="method">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-green-100 dark:bg-green-500/20 rounded-lg text-green-600 dark:text-green-400">
                                            💳
                                        </div>
                                        <span class="font-bold text-gray-800 dark:text-white">Card</span>
                                    </div>
                                    <div class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 transition-colors"
                                         :class="method === 'card' ? 'border-blue-500 bg-blue-500' : 'border-gray-300 dark:border-white/20'"></div>
                                </label>
                            </div>
                            @error('payment_method') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label for="notes" class="block font-medium text-gray-700 dark:text-gray-300 mb-2">Order Notes (Optional)</label>
                            <textarea name="notes" id="notes" rows="2"
                                class="w-full px-4 py-3 rounded-lg border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Any special instructions for your delivery">{{ old('notes') }}</textarea>
                            @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-4">
                            <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition duration-300 shadow-lg transform hover:-translate-y-1 active:scale-95">
                                Place Order (₹{{ $grandTotal }})
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ORDER SUMMARY --}}
            <div class="w-full md:w-[400px]">
                <div class="bg-gray-50 dark:bg-white/5 shadow-lg rounded-xl p-6 border border-gray-100 dark:border-white/10 sticky top-10">
                    <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-white border-b border-gray-200 dark:border-white/10 pb-4">
                        Order Summary 🛍️
                    </h3>

                    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach ($cartItems as $item)
                            <div class="flex items-center gap-4 py-2 border-b border-gray-100 dark:border-white/5 last:border-0">
                                <div class="w-12 h-12 bg-gray-200 dark:bg-white/10 rounded-md overflow-hidden flex-shrink-0">
                                    @if($item->product->image)
                                        <img src="{{ $item->product->image ? Storage::url('images/' . $item->product->image) : 'https://via.placeholder.com/250' }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-xs text-gray-500">No Image</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white truncate">{{ $item->product->name }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Qty: {{ $item->quantity }} × ₹{{ $item->unit_price }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-bold text-gray-800 dark:text-white">₹{{ $item->total_price }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 space-y-3">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span>₹{{ $grandTotal }}</span>
                        </div>
                        @if($totalSavings > 0)
                            <div class="flex justify-between text-green-600 dark:text-green-400 text-sm">
                                <span>You Save</span>
                                <span>-₹{{ $totalSavings }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Shipping</span>
                            <span class="font-medium text-green-600">FREE</span>
                        </div>
                        <div class="pt-4 border-t border-gray-200 dark:border-white/10 flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-800 dark:text-white uppercase tracking-tight">Grand Total</span>
                            <span class="text-2xl font-black text-green-600 dark:text-green-400">₹{{ $grandTotal }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
    </style>
</x-app-layout>
