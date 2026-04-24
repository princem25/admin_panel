<x-app-layout>
    <div class="min-h-screen py-10 px-4">
        <div class="max-w-5xl mx-auto flex flex-col gap-10">


            
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white flex items-center gap-3">
                    <a href="{{ route('orders.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 dark:bg-white/10 dark:hover:bg-white/20 rounded-lg transition text-sm">
                        ⬅ Back
                    </a>
                    Order #{{ $order->id }}
                </h1>
                <div class="flex items-center gap-4">
                    @if($order->status !== 'cancelled')
                        <div class="flex flex-col items-end gap-1">
                            <a href="{{ $downloadUrl }}" class="mt-5 no-transition px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs transition shadow-lg flex items-center gap-2">
                                📄 Invoice
                            </a>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500">Link expires in 10 mins</span>
                        </div>
                    @endif

                    @if($order->status === 'pending')
                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order? All stock will be restored.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-xs transition shadow-lg transform hover:-translate-y-1 active:scale-95">
                                ❌ Cancel Order
                            </button>
                        </form>
                    @endif

                    <span id="order-status-badge" class="px-4 py-2 
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-500/20 dark:text-yellow-400 dark:border-yellow-500/30' : '' }}
                        {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-500/20 dark:text-blue-400 dark:border-blue-500/30' : '' }}
                        {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-500/20 dark:text-purple-400 dark:border-purple-500/30' : '' }}
                        {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800 border-green-200 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30' : '' }}
                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800 border-red-200 dark:bg-red-500/20 dark:text-red-400 dark:border-red-500/30' : '' }}
                        rounded-full font-bold uppercase tracking-widest text-xs border shadow-sm">
                        {{ $order->status }}
                    </span>
                   
                </div>
            </div>

            <div id="alert-section">

                {{-- Flash message handled globally --}}

            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- DETAILS CARD --}}
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-white/10 shadow-xl rounded-md p-8 border border-gray-200 dark:border-white/20">
                        <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-white border-b border-gray-200 dark:border-white/10 pb-4">
                            Items Purchased
                        </h3>
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center gap-6 py-4 border-b border-gray-100 dark:border-white/5 last:border-0 hover:bg-gray-50 dark:hover:bg-white/5 rounded-xl px-2 transition">
                                    <div class="w-20 h-20 bg-gray-200 dark:bg-white/10 rounded-2xl overflow-hidden flex-shrink-0 shadow-sm">
                                        @if($item->product->image)
                                            <img src="{{ $item->product->image ? Storage::url('images/' . $item->product->image) : 'https://via.placeholder.com/250' }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xs text-gray-500">No Image</div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-lg font-bold text-gray-800 dark:text-white truncate">{{ $item->product->name }}</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">₹{{ $item->price }} × {{ $item->quantity }} units</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-extrabold text-blue-600 dark:text-blue-400">{{ Number::currency($item->price * $item->quantity, 'INR') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($order->notes)
                        <div class="bg-blue-50 dark:bg-blue-500/10 shadow-md rounded-2xl p-6 border border-blue-100 dark:border-blue-500/20 italic text-blue-800 dark:text-blue-300">
                           "{{ $order->notes }}"
                        </div>
                    @endif
                </div>

                {{-- SUMMARY CARD --}}
                <div class="space-y-6">
                    <div class="bg-white dark:bg-white/10 shadow-xl rounded-lg p-8 border border-gray-200 dark:border-white/20">
                        <h3 class="text-lg font-bold mb-6 text-gray-800 dark:text-white border-b border-gray-200 dark:border-white/10 pb-4">
                           Full Summary
                        </h3>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between text-gray-500 text-sm">
                                <span>Place Date</span>
                                <span class="font-bold text-gray-800 dark:text-white">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex justify-between text-gray-500 text-sm">
                                <span>Status</span>
                                <span id="update" class="font-bold text-yellow-600 dark:text-yellow-400 uppercase">{{ $order->status }}</span>
                            </div>
                            <div class="flex justify-between text-gray-500 text-sm">
                                <span>Payment Method</span>
                                <span class="font-bold text-gray-800 dark:text-white uppercase">{{ $order->payment_method }}</span>
                            </div>
                            <div class="pt-4 border-t border-gray-100 dark:border-white/5 flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-800 dark:text-white uppercase tracking-tight">
                                    {{ $order->payment_method === 'cod' ? 'Total Amount' : 'Amount Paid' }}
                                </span>
                                <span class="text-2xl font-black text-green-600 dark:text-green-400">₹{{ $order->total_amount }}</span>
                            </div>

                            @if($order->status === 'cancelled')
                                <div class="mt-4 p-4 bg-red-50 dark:bg-red-500/10 rounded-2xl border border-red-100 dark:border-red-500/20 text-xs font-bold text-red-600 dark:text-red-400 flex items-center gap-3">
                                    <span class="text-xl">ℹ️</span>
                                    <span>
                                        @if($order->payment_method === 'cod')
                                            This order was cancelled. No payment was collected.
                                        @else
                                            Your refund of {{ Number::currency($order->total_amount, 'INR') }} will be processed back to your original {{ strtoupper($order->payment_method) }} payment method within 5-7 business days.
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="pt-6 border-t border-gray-200 dark:border-white/10">
                            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Delivery Address</h4>
                            <div class="text-sm text-gray-800 dark:text-white font-medium">
                                <p class="mb-1">{{ $order->full_name }}</p>
                                <p class="text-gray-600 dark:text-gray-400 whitespace-pre-line leading-relaxed mb-1">{{ $order->shipping_address }}</p>
                                <p class="text-gray-600 dark:text-gray-400">📞 {{ $order->phone }}</p>
                            </div>
                        </div>
                    </div>

                    @if($current_logged_user->role === 'admin')
                        <div class="bg-gray-50 dark:bg-white/5 rounded-3xl p-6 border border-gray-200 dark:border-white/10">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Customer Details</h4>
                            <p class="text-sm text-gray-800 dark:text-white font-bold">{{ $order->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        if (window.Echo) {
            window.Echo.private('order.{{ $order->id }}')
                .listen('.order.status.updated', (data) => {
                    const badge = window.$('#order-status-badge');

                    const status = window.$('#update');
                    
                    // Simple replacement of text
                    badge.text(data.orderStatus);
                    status.text(data.orderStatus);

                    // Reset all colors and apply the new ones
                    const baseClasses = "px-4 py-2 rounded-full font-bold uppercase tracking-widest text-xs border shadow-sm";
                    let newColorClasses = "";
                    
                    switch (data.orderStatus) {
                        case 'pending': newColorClasses = 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-500/20 dark:text-yellow-400 dark:border-yellow-500/30'; break;
                        case 'processing': newColorClasses = 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-500/20 dark:text-blue-400 dark:border-blue-500/30'; break;
                        case 'shipped': newColorClasses = 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-500/20 dark:text-purple-400 dark:border-purple-500/30'; break;
                        case 'delivered': newColorClasses = 'bg-green-100 text-green-800 border-green-200 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30'; break;
                        case 'cancelled': newColorClasses = 'bg-red-100 text-red-800 border-red-200 dark:bg-red-500/20 dark:text-red-400 dark:border-red-500/30'; break;
                    }

                    badge.attr('class', baseClasses + " " + newColorClasses);

                    // Show toast popup
                    const $toast = window.$(`
                        <div class="bg-blue-600 border border-blue-400 text-white px-6 py-4 rounded-xl shadow-2xl max-w-sm mb-3">
                            <h4 class="font-bold text-lg flex items-center gap-2">
                                <span>🔔</span> Status Updated!
                            </h4>
                            <p class="text-sm mt-1">Your order is now <strong>${data.orderStatus.toUpperCase()}</strong>.</p>
                        </div>
                    `);

                    window.$('#toast-container').append($toast);

                    setTimeout(() => {
                        $toast.fadeOut(400, function() {
                            window.$(this).remove();
                        });
                    }, 5000);
                });
        }
    </script>
</x-app-layout>
