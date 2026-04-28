<x-app-layout>
    <div class="min-h-screen py-16 px-4 flex items-center justify-center">
        <div class="max-w-3xl w-full bg-white dark:bg-white/10 shadow-2xl rounded-3xl p-10 border border-gray-200 dark:border-white/20 text-center">
            
            {{-- Success Icon --}}
            <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 dark:bg-green-500/20 rounded-full mb-8 animate-pulse">
                <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-4xl font-extrabold mb-4 text-gray-800 dark:text-white">
                Thank You for Your Order! 🎉
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-10">
                {{ __('Order # :id placed on :date', ['id' => $order->id, 'date' => $order->created_at->isoFormat('LL')]) }}
                <br>
                We'll notify you as soon as it's on its way!
            </p>

            {{-- ORDER SUMMARY --}}
            <div class="bg-gray-50 dark:bg-white/5 rounded-2xl p-8 mb-10 text-left border border-gray-100 dark:border-white/10">
                <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-white border-b border-gray-200 dark:border-white/10 pb-4 flex items-center gap-2">
                    Order Details
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Shipping To</h4>
                        <p class="text-gray-800 dark:text-white font-medium">{{ $order->full_name }}</p>
                        <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line">{{ $order->shipping_address }}</p>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">📞 {{ $order->phone }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Payment Details</h4>
                        <p class="text-gray-800 dark:text-white font-medium">Method: <span class="text-gray-600 dark:text-gray-400 font-bold uppercase tracking-widest text-xs">{{ $order->payment_method }}</span></p>
                        <p class="text-gray-800 dark:text-white font-medium mt-1">
                            {{ $order->payment_method === 'cod' ? 'Total Amount' : 'Amount Paid' }}: 
                            <span class="text-3xl font-black text-green-600 dark:text-green-400">{{ Number::currency($order->total_amount, 'INR') }}</span>
                        </p>
                        <p class="text-gray-800 dark:text-white font-medium mt-1">Status: <span class="text-yellow-600 dark:text-yellow-400 uppercase tracking-widest text-xs font-bold">{{ $order->status }}</span></p>
                    </div>
                </div>

                @if($order->notes)
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-white/10">
                        <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Additional Notes</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-sm italic">"{{ $order->notes }}"</p>
                    </div>
                @endif
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('user.products') }}" 
                    class="w-full sm:w-auto px-10 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition shadow-lg transform hover:-translate-y-1 active:scale-95">
                    Continue Shopping
                </a>
                
                <a href="{{ route('orders.index') }}" 
                    class="w-full sm:w-auto px-10 py-4 bg-gray-200 hover:bg-gray-300 dark:bg-white/10 dark:hover:bg-white/20 text-gray-800 dark:text-white font-bold rounded-xl transition shadow-md">
                    View My Orders
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
