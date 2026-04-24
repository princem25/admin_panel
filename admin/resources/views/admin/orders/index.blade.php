<x-app-layout>
    <div class="min-h-screen py-10 px-4">
        <div class="max-w-7xl mx-auto space-y-6">

            {{-- HEADER & SEARCH --}}
            <div class="bg-white dark:bg-white/10 shadow-xl rounded-3xl p-8 border border-gray-200 dark:border-white/20">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white flex items-center gap-3">
                            Order Management 📦
                        </h2>
                        <p class="text-gray-500 dark:text-white/60 text-sm mt-1">Manage and track all customer orders.</p>
                    </div>

                    <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 flex-1 md:max-w-2xl">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                🔍
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 dark:border-white/10 dark:bg-white/5 dark:text-white focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Search by Order ID or Customer name...">
                        </div>
                        
                        <select name="status" onchange="this.form.submit()"
                            class="rounded-xl border-gray-200 dark:border-white/10 dark:bg-[#2f4f54] dark:text-white focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>

                        @if(request()->anyFilled(['search', 'status']))
                            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-white/10 dark:hover:bg-white/20 text-gray-700 dark:text-white rounded-xl transition text-center">
                                Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- ORDERS TABLE --}}
            <div class="bg-white dark:bg-white/10 shadow-xl rounded-3xl overflow-hidden border border-gray-200 dark:border-white/20">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-white/5 border-b border-gray-200 dark:border-white/10 text-gray-500 dark:text-white/70 text-xs font-bold uppercase tracking-wider">
                                <th class="p-6">Order ID</th>
                                <th class="p-6">Customer</th>
                                <th class="p-6">Amount</th>
                                <th class="p-6">Status</th>
                                <th class="p-6">Created At</th>
                                <th class="p-6 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition group">
                                    <td class="p-6">
                                        <span class="font-bold text-blue-600 dark:text-blue-400">#{{ $order->id }}</span>
                                    </td>
                                    <td class="p-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold">
                                                {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $order->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-6 font-bold text-green-600 dark:text-green-400">
                                        {{ Number::currency($order->total_amount, 'INR') }}
                                    </td>
                                    <td class="p-6">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full uppercase tracking-widest border
                                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-500/20 dark:text-yellow-400 dark:border-yellow-500/30' : '' }}
                                            {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-500/20 dark:text-blue-400 dark:border-blue-500/30' : '' }}
                                            {{ $order->status === 'shipped' ? 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-500/20 dark:text-purple-400 dark:border-purple-500/30' : '' }}
                                            {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800 border-green-200 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800 border-red-200 dark:bg-red-500/20 dark:text-red-400 dark:border-red-500/30' : '' }}
                                        ">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="p-6 text-sm text-gray-500 dark:text-white/60">
                                        {{ date('d M, Y H:i A', strtotime($order->created_at)) }}<br>
                                        <span class="text-xs">{{ $order->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="p-6 text-center">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" 
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition text-sm font-bold shadow-md transform hover:-translate-y-1 active:scale-95">
                                           ⚙️ Manage
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-20 text-center text-gray-500 dark:text-white/60">
                                        <div class="text-5xl mb-4">📭</div>
                                        No orders found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="p-6 border-t border-gray-200 dark:border-white/10">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
