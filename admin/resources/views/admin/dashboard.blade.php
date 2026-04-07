<x-app-layout>

    <!-- 🌌 Background -->
    <div
        class="min-h-screen relative overflow-hidden 

    bg-white text-gray-900
    dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white">

        <!-- Glow Effects (only dark) -->
        <div
            class="hidden dark:block absolute w-[500px] h-[500px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10">
        </div>
        <div
            class="hidden dark:block absolute w-[500px] h-[500px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10">
        </div>

        <div class="relative max-w-5xl mx-auto px-6 py-10">

            <!-- Header -->
            <div class="flex justify-between h-10 mb-8">
                <h1 class="text-3xl font-bold mb-8">
                    Hello, Admin 👋
                </h1>

                <a href="{{ route('admin.products.export') }}"
                    class="inline-block px-5 py-2 bg-green-600 text-white font-medium rounded-lg shadow hover:bg-green-700 hover:shadow-md transition duration-200">
                    Download CSV
                </a>
            </div>


            <!-- 🚀 Action Card -->
            <div
                class="rounded-xl p-6 mb-6 flex items-center justify-between transition shadow

            bg-white border border-gray-200 hover:scale-[1.02]
            dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-lg">

                <div>
                    <h2 class="text-lg font-semibold">
                        Product Management
                    </h2>
                    <p class="text-gray-600 dark:text-white/70 text-sm">
                        Create, edit and manage your products
                    </p>

                </div>

                <form action="{{ route('products.index') }}">
                    <button type="submit"
                        class="px-4 py-2 rounded-lg transition shadow text-white

                        bg-blue-600 hover:bg-blue-700
                        dark:bg-cyan-500 dark:hover:bg-cyan-600">
                        Manage Products →
                    </button>
                </form>



            </div>
            {{-- Order Analytics Section --}}
            <div class="mb-8 border-b border-gray-200 dark:border-white/10 pb-8">
                <h2 class="text-xl font-bold mb-4">Order Analytics</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- Total Orders -->
                    <div class="p-5 rounded-xl transition bg-white border border-gray-200 shadow-sm dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md">
                        <div class="text-blue-500 text-2xl mb-2">📦</div>
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-white/60 uppercase tracking-wider">Total Orders</h3>
                        <p class="text-2xl font-bold mt-1 text-gray-900 dark:text-white">{{ $totalOrders }}</p>
                    </div>

                    <!-- Genuine Orders -->
                    <div class="p-5 rounded-xl transition bg-white border border-gray-200 shadow-sm dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md">
                        <div class="text-green-500 text-2xl mb-2">✅</div>
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-white/60 uppercase tracking-wider">Genuine Orders</h3>
                        <p class="text-2xl font-bold mt-1 text-gray-900 dark:text-white">{{ $genuineOrders }}</p>
                    </div>

                    <!-- Cancelled Orders -->
                    <div class="p-5 rounded-xl transition bg-white border border-gray-200 shadow-sm dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md">
                        <div class="text-red-500 text-2xl mb-2">❌</div>
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-white/60 uppercase tracking-wider">Cancelled Orders</h3>
                        <p class="text-2xl font-bold mt-1 text-gray-900 dark:text-white">{{ $cancelledOrders }}</p>
                    </div>

                    <!-- Total Revenue -->
                    <div class="p-5 rounded-xl transition bg-white border border-gray-200 shadow-sm dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md">
                        <div class="text-yellow-500 text-2xl mb-2">💰</div>
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-white/60 uppercase tracking-wider">Total Revenue</h3>
                        <p class="text-2xl font-bold mt-1 text-gray-900 dark:text-white">@currency($totalRevenue)</p>
                    </div>
                </div>

                <!-- Revenue by Payment Method -->
                @if($revenueByPaymentMethod->isNotEmpty())
                    <h3 class="text-lg font-bold mt-8 mb-4">Revenue by Payment Method</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($revenueByPaymentMethod as $revenue)
                            <div class="p-4 rounded-xl flex justify-between items-center transition bg-gray-50 hover:bg-gray-100 dark:bg-white/5 dark:hover:bg-white/10 border border-gray-200 dark:border-white/10 shadow-sm">
                                <span class="font-medium capitalize text-gray-700 dark:text-gray-300">{{ str_replace('_', ' ', $revenue->payment_method) }}</span>
                                <span class="font-bold text-green-600 dark:text-green-400">@currency($revenue->total)</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <h2 class="text-xl font-bold mb-4">Categories Overview</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach ($categorySummary as $item)
                    <div
                        class="p-5 rounded-xl transition bg-white border border-gray-200 shadow-sm dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md hover:shadow-md dark:hover:shadow-lg">

                        <!-- Icon -->
                        <div class="text-2xl mb-3">📦</div>

                        <!-- Category Name -->
                        <h2 class="text-lg font-semibold">
                            {{ $item->name }}
                        </h2>

                        <!-- Product Count -->
                        <p class="text-sm mt-1 
            text-gray-600 dark:text-white/70">
                            {{ $item->total }} products
                        </p>

                    </div>
                @endforeach

            </div>

            {{-- System Logs + Company Info Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                {{-- System Log Viewer Card --}}
                <div
                    class="rounded-xl p-6 flex items-center justify-between transition shadow
                            bg-white border border-gray-200 hover:scale-[1.01]
                            dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-lg">
                    <div>
                        <h2 class="text-lg font-semibold">System Logs</h2>
                        <p class="text-gray-600 dark:text-white/70 text-sm">View recent application log entries</p>
                    </div>
                    <a href="{{ route('admin.logs') }}"
                        class="px-4 py-2 rounded-lg transition shadow text-white bg-purple-600 hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-sm font-medium">
                        View Logs →
                    </a>
                </div>

                {{-- Company Info Card --}}
                <div
                    class="rounded-xl p-6 shadow
                            bg-white border border-gray-200
                            dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-lg">
                    <h2 class="text-lg font-semibold mb-4">Company Information</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        <div class="p-3 rounded-lg bg-gray-100 dark:bg-white/5">
                            <p class="text-xs text-gray-500 dark:text-white/60 mb-0.5">Name</p>
                            <p class="font-medium">{{ config('company.name') }}</p>
                        </div>

                        <div class="p-3 rounded-lg bg-gray-100 dark:bg-white/5">
                            <p class="text-xs text-gray-500 dark:text-white/60 mb-0.5">Email</p>
                            <p class="font-medium">{{ config('company.email') }}</p>
                        </div>

                        <div class="p-3 rounded-lg bg-gray-100 dark:bg-white/5">
                            <p class="text-xs text-gray-500 dark:text-white/60 mb-0.5">City</p>
                            <p class="font-medium">{{ config('company.address.city') }}</p>
                        </div>

                        <div class="p-3 rounded-lg bg-gray-100 dark:bg-white/5">
                            <p class="text-xs text-gray-500 dark:text-white/60 mb-0.5">Tax</p>
                            <p class="font-medium text-green-600 dark:text-green-400">{{ config('company.tax') }}%</p>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

    <script type="module">
        if (window.Echo) {
            window.Echo.private('admin.orders')
                .listen('.order.placed', (data) => {
                    const $toast = window.$(`
                        <div class="bg-green-600 border border-green-400 text-white px-6 py-4 rounded-xl shadow-2xl max-w-sm mb-3">
                            <h4 class="font-bold text-lg mb-2 flex items-center gap-2">
                                <span>🛒</span> New Order Received!
                            </h4>
                            <p class="text-sm"><strong>Customer:</strong> ${data.customerName}</p>
                            <p class="text-sm mt-1"><strong>Total:</strong> Rs.${data.orderTotal}</p>
                            <p class="text-sm mt-1"><strong>Items:</strong> ${data.itemsCount}</p>
                        </div>
                    `);

                    window.$('#toast-container').append($toast);

                    setTimeout(() => {
                        $toast.fadeOut(400, function() {
                            window.$(this).remove();
                        });
                    }, 8000);
                });
        }
    </script>
</x-app-layout>
