<x-app-layout>
    <div class="min-h-screen py-10 px-4">
        <div class="max-w-6xl mx-auto bg-white dark:bg-white/10 shadow-lg rounded-xl p-8 border border-gray-200 dark:border-white/20">
            
            <h2 class="text-3xl font-extrabold mb-8 text-gray-800 dark:text-white flex items-center justify-between">
                <span>{{ auth()->user()->role === 'admin' ? 'Total Orders 📦' : 'My Order History 📦' }}</span>
                @if(auth()->user()->role !== 'admin')
                    <a href="{{ route('user.products') }}" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition shadow-md">
                        Shop More
                    </a>
                @endif
            </h2>

            <div id="alert-section">
                <x-flash-message />
            </div>

            @if ($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full border rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-white text-left">
                                <th class="p-4">Order ID</th>
                                @if(auth()->user()->role === 'admin')
                                    <th class="p-4">Customer</th>
                                @endif
                                <th class="p-4">Date</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Total</th>
                                <th class="p-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach ($orders as $order)
                                <tr class="text-gray-800 dark:text-white hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                    <td class="p-4 font-bold text-blue-600 dark:text-blue-400">
                                        #{{ $order->id }}
                                    </td>
                                    @if(auth()->user()->role === 'admin')
                                        <td class="p-4">
                                            <div class="flex flex-col">
                                                <span class="font-medium">{{ $order->user->name }}</span>
                                                <span class="text-xs text-gray-500">{{ $order->user->email }}</span>
                                            </div>
                                        </td>
                                    @endif
                                    <td class="p-4 text-sm">
                                        {{ $order->created_at->format('d M Y, h:i A') }}
                                    </td>
                                    <td class="p-4">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full uppercase tracking-wider 
                                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400' : 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400' }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-extrabold text-green-600 dark:text-green-400">
                                        ₹{{ $order->total_amount }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="{{ route('orders.show', $order->id) }}" 
                                            class="inline-block px-4 py-1 bg-gray-200 hover:bg-gray-300 dark:bg-white/10 dark:hover:bg-white/20 text-gray-800 dark:text-white rounded transition text-sm">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-20 bg-gray-50 dark:bg-white/5 rounded-3xl border-2 border-dashed border-gray-200 dark:border-white/10">
                    <div class="text-5xl mb-4">📭</div>
                    <p class="text-xl text-gray-500 dark:text-white/70">No orders found yet.</p>
                    @if(auth()->user()->role !== 'admin')
                        <a href="{{ route('user.products') }}" class="mt-4 inline-block text-blue-600 hover:underline">Start shopping</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
