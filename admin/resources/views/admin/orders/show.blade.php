<x-app-layout>
    <div class="min-h-screen py-10 px-4">
        <div class="max-w-7xl mx-auto space-y-8">
            
            {{-- Top Toolbar --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.orders.index') }}" class="p-3 bg-white dark:bg-white/10 hover:bg-gray-100 dark:hover:bg-white/20 rounded-2xl transition shadow-md group">
                        <span class="group-hover:-translate-x-1 inline-block transition">⬅</span>
                    </a>
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white">Order #{{ $order->id }}</h1>
                        <p class="text-sm text-gray-500 dark:text-white/60 font-medium uppercase tracking-widest">
                            Customer: {{ $order->user->name }} • Place Date: {{ $order->created_at->format('d M, Y h:i A') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @if($order->status !== 'cancelled')
                        <a href="{{ route('orders.invoice', $order->id) }}" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-bold text-xs transition shadow-md flex items-center gap-2">
                            📄 Download Invoice
                        </a>
                    @endif
                    
                    <span class="px-5 py-2 text-sm font-bold rounded-full uppercase tracking-widest border shadow-sm
                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-500/20 dark:text-yellow-400 dark:border-yellow-500/30' : '' }}
                        {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-500/20 dark:text-blue-400 dark:border-blue-500/30' : '' }}
                        {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-500/20 dark:text-purple-400 dark:border-purple-500/30' : '' }}
                        {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800 border-green-300 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30' : '' }}
                        {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800 border-red-300 dark:bg-red-500/20 dark:text-red-400 dark:border-red-500/30' : '' }}
                    ">
                        {{ $order->status }}
                    </span>
                </div>
            </div>


            {{-- Flash Message handled globally by x-app-layout --}}


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- LEFT COLUMN: ORDER CONTENT --}}
                <div class="lg:col-span-2 space-y-8">
                    
                    {{-- PRODUCTS LIST --}}
                    <div class="bg-white dark:bg-white/10 shadow-xl rounded-3xl p-8 border border-gray-200 dark:border-white/20">
                        <h3 class="text-xl font-extrabold mb-8 text-gray-800 dark:text-white border-b border-gray-200 dark:border-white/10 pb-4">
                           Ordered Items 📦
                        </h3>
                        <div class="space-y-6">
                            @foreach($order->items as $item)
                                <div class="flex items-center gap-8 py-6 border-b border-gray-100 dark:border-white/5 last:border-0 hover:bg-gray-50 dark:hover:bg-white/5 rounded-2xl px-4 transition">
                                    <div class="w-24 h-24 bg-gray-200 dark:bg-white/10 rounded-2xl overflow-hidden flex-shrink-0 shadow-lg border-2 border-white dark:border-white/10">
                                        @if($item->product->image)
                                            <img src="{{ $item->product->image ? Storage::url('images/' . $item->product->image) : 'https://via.placeholder.com/250' }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">No Image</div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-xl font-bold text-gray-800 dark:text-white truncate">{{ $item->product->name }}</h4>
                                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">₹{{ number_format($item->price, 2) }} × {{ $item->quantity }} units</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-extrabold text-blue-600 dark:text-blue-400">₹{{ number_format($item->price * $item->quantity, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- GRAND TOTAL --}}
                        <div class="mt-10 pt-8 border-t-2 border-gray-100 dark:border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="bg-gray-50 dark:bg-white/5 px-6 py-4 rounded-2xl border border-gray-100 dark:border-white/10">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Payment Method</p>
                                <p class="text-lg font-extrabold text-gray-800 dark:text-white uppercase">{{ $order->payment_method }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">
                                    {{ $order->payment_method === 'cod' ? 'Total Amount' : 'Amount Paid' }}
                                </p>
                                <p class="text-3xl font-black text-green-600 dark:text-green-400 highlight-price">₹{{ number_format($order->total_amount, 2) }}</p>
                            </div>

                            @if($order->status === 'cancelled')
                                <div class="mt-6 p-4 bg-red-50 dark:bg-red-500/10 rounded-2xl border border-red-100 dark:border-red-500/20 text-xs font-bold text-red-600 dark:text-red-400 flex items-center gap-3">
                                    <span class="text-xl">ℹ️</span>
                                    <span>
                                        @if($order->payment_method === 'cod')
                                            No payment was collected for this order.
                                        @else
                                            Refund of ₹{{ number_format($order->total_amount, 2) }} to {{ strtoupper($order->payment_method) }} should be processed.
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- STATUS HISTORY TIMELINE --}}
                    <div class="bg-white dark:bg-white/10 shadow-xl rounded-3xl p-8 border border-gray-200 dark:border-white/20">
                        <h3 class="text-xl font-extrabold mb-8 text-gray-800 dark:text-white border-b border-gray-200 dark:border-white/10 pb-4">
                           Order Timeline 🕒
                        </h3>
                        
                        <div class="relative space-y-8 pl-8 before:content-[''] before:absolute before:left-3 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-200 dark:before:bg-white/10">
                            @forelse($order->statusHistories as $history)
                                <div class="relative">
                                    <div class="absolute -left-[2.15rem] top-1 w-4 h-4 rounded-full border-2 border-white dark:border-[#2f4f54] bg-blue-500 shadow-sm z-10"></div>
                                    <p class="text-sm font-black text-gray-800 dark:text-white flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400 uppercase">{{ $history->status }}</span>
                                        <span class="text-gray-400 font-normal">by</span>
                                        {{ $history->user->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1 font-medium">{{ $history->created_at->format('d M, Y • h:i A') }}</p>
                                    @if($history->notes)
                                        <div class="mt-3 p-4 bg-gray-50 dark:bg-white/5 rounded-2xl text-sm text-gray-600 dark:text-white/70 italic border border-gray-100 dark:border-white/5">
                                            "{{ $history->notes }}"
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-gray-500 italic text-sm">Initial order placement.</p>
                            @endforelse
                            <div class="relative">
                                    <div class="absolute -left-[2.15rem] top-1 w-4 h-4 rounded-full border-2 border-white dark:border-[#2f4f54] bg-green-500 shadow-sm z-10"></div>
                                    <p class="text-sm font-black text-gray-800 dark:text-white">Order Placed</p>
                                    <p class="text-xs text-gray-500 mt-1 font-medium">{{ $order->created_at->format('d M, Y • h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: SIDEBARS --}}
                <div class="space-y-8">
                    
                    {{-- STATUS UPDATE ACTION --}}
                    <div class="bg-white dark:bg-white/10 shadow-xl rounded-3xl p-8 border border-gray-200 dark:border-white/20">
                        <h3 class="text-lg font-bold mb-6 text-gray-800 dark:text-white flex items-center gap-2">
                            Update Order ⚙️
                        </h3>
                        
                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')
                            
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Order Status</label>
                                <select name="status" class="w-full rounded-2xl border-gray-200 dark:border-white/10 dark:bg-[#2f4f54] dark:text-white font-bold h-12 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Public Timeline Note</label>
                                <input type="text" name="history_note" placeholder="Visible in timeline..." 
                                    class="w-full h-12 px-4 rounded-2xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="pt-4 border-t border-gray-100 dark:border-white/5">
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-3">Private Admin Note 🔓</label>
                                <textarea name="admin_note" rows="4" placeholder="Internal communication only..."
                                    class="w-full px-4 py-3 rounded-2xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500">{{ $order->admin_note }}</textarea>
                            </div>

                            <button type="submit" 
                                class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-2xl transition shadow-xl transform hover:-translate-y-1 active:scale-95">
                                Save Changes
                            </button>
                        </form>
                    </div>

                    {{-- CUSTOMER & SHIPPING INFO --}}
                    <div class="bg-gray-800 text-white shadow-xl rounded-3xl p-8 space-y-8">
                        <div>
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Customer Details</h4>
                            <p class="text-xl font-bold">{{ $order->user->name }}</p>
                            <p class="text-blue-400 text-sm italic underline">{{ $order->user->email }}</p>
                        </div>
                        
                        <div class="pt-8 border-t border-white/10">
                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Shipping Information</h4>
                            <div class="space-y-4">
                                <p class="text-sm"><span class="text-gray-400">Recipient:</span> {{ $order->full_name }}</p>
                                <p class="text-sm leading-relaxed"><span class="text-gray-400">Address:</span><br>{{ $order->shipping_address }}</p>
                                <p class="text-sm"><span class="text-gray-400">Phone:</span> {{ $order->phone }}</p>
                            </div>
                        </div>

                        @if($order->notes)
                            <div class="pt-8 border-t border-white/10">
                                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Customer's Note</h4>
                                <p class="text-sm italic text-gray-300">"{{ $order->notes }}"</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
