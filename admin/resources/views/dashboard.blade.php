<x-app-layout>

    <div
        class="relative min-h-screen 

    bg-white text-gray-900
    dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white">

        <div class="w-[80%] mx-auto py-10">

            <!-- Header -->
            <div class="mb-4">
                <h1 class="text-2xl font-semibold text-blue-600 dark:text-cyan-400">
                    Welcome back, {{ auth()->user()->name }} 👋
                </h1>
                <p class="text-gray-600 dark:text-white/70">
                    Here is an overview of your orders , spending and available products.
                </p>
            </div>

            <!-- Personal Order Metrics -->
            <div class="mb-10 pb-8 border-b border-gray-200 dark:border-white/10">
                <h2 class="text-xl font-bold mb-4">My Orders</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                    <!-- Total Orders -->
                    <div class="p-4 rounded-xl transition bg-white border border-gray-200 shadow-sm dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md">
                        <div class="text-blue-500 text-xl mb-1">📦</div>
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-white/60 uppercase tracking-wider">Total Orders</h3>
                        <p class="text-xl font-bold mt-1 text-gray-900 dark:text-white">{{ $totalOrders }}</p>
                    </div>

                    <!-- Active Orders -->
                    <div class="p-4 rounded-xl transition bg-white border border-gray-200 shadow-sm dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md">
                        <div class="text-orange-500 text-xl mb-1">⏳</div>
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-white/60 uppercase tracking-wider">Active Orders</h3>
                        <p class="text-xl font-bold mt-1 text-gray-900 dark:text-white">{{ $activeOrders }}</p>
                    </div>

                    <!-- Delivered Orders -->
                    <div class="p-4 rounded-xl transition bg-white border border-gray-200 shadow-sm dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md">
                        <div class="text-green-500 text-xl mb-1">✅</div>
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-white/60 uppercase tracking-wider">Delivered</h3>
                        <p class="text-xl font-bold mt-1 text-gray-900 dark:text-white">{{ $deliveredOrders }}</p>
                    </div>

                    <!-- Cancelled Orders -->
                    <div class="p-4 rounded-xl transition bg-white border border-gray-200 shadow-sm dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md">
                        <div class="text-red-500 text-xl mb-1">❌</div>
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-white/60 uppercase tracking-wider">Cancelled</h3>
                        <p class="text-xl font-bold mt-1 text-gray-900 dark:text-white">{{ $cancelledOrders }}</p>
                    </div>

                    <!-- Total Spent -->
                    <div class="p-4 rounded-xl transition bg-white border border-gray-200 shadow-sm dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md">
                        <div class="text-yellow-500 text-xl mb-1">💰</div>
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-white/60 uppercase tracking-wider">Total Spent</h3>
                        <p class="text-xl font-bold mt-1 text-gray-900 dark:text-white">@currency($totalSpent)</p>
                    </div>
                </div>
            </div>

            <!-- Header for Shop -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    Shop Categories 🛒
                </h2>
                <p class="text-gray-600 dark:text-white/70">
                    Category-wise overview of total products
                </p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

                @foreach ($categorySummary as $item)
                    <div
                        class="p-6 rounded-xl 

                    bg-white border border-gray-200 shadow-sm
                    dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md

                    hover:shadow-md dark:hover:shadow-lg transition">

                        <div class="text-2xl mb-2">📦</div>

                        <h2 class="text-lg font-semibold">
                            {{ $item->name }}
                        </h2>

                        <p class="text-sm mt-1 
                        text-gray-600 dark:text-white/70">
                            {{ $item->total }} products
                        </p>

                    </div>
                @endforeach

            </div>

        </div>
    </div>

</x-app-layout>
