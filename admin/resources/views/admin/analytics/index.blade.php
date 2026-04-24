<x-app-layout>
    <div class="min-h-screen bg-white text-gray-900 dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white py-10 px-6">
        
        <!-- Glow Effects (only dark mode) -->
        <div class="hidden dark:block absolute w-[500px] h-[500px] bg-cyan-400 opacity-10 blur-3xl rounded-full top-10 left-10 pointer-events-none"></div>
        <div class="hidden dark:block absolute w-[500px] h-[500px] bg-blue-500 opacity-10 blur-3xl rounded-full bottom-10 right-10 pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
                <div>
                    <h1 class="text-4xl font-extrabold tracking-tight">Sales Analytics</h1>
                    <p class="text-gray-500 dark:text-white/60 mt-2">Comprehensive reports and data-driven insights</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 transition font-medium border border-gray-200 dark:border-white/10">
                        ← Dashboard
                    </a>
                </div>
            </div>


            <div class="grid grid-cols-1 gap-10">

                <!-- 1. Monthly Sales Report -->
                <section class="bg-white dark:bg-white/5 dark:backdrop-blur-xl border border-gray-200 dark:border-white/10 rounded-2xl shadow-xl overflow-hidden transition-all hover:shadow-2xl">
                    <div class="p-6 border-b border-gray-200 dark:border-white/10 flex justify-between items-center bg-gray-50/50 dark:bg-white/5">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">📅</span>
                            <h2 class="text-xl font-bold">Monthly Sales Report</h2>
                        </div>
                        <a href="{{ route('admin.analytics.export', 'monthly-sales') }}" class="no-transition px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-bold shadow-lg transition transform hover:scale-105 active:scale-95">
                            Export CSV
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-white/60 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">Month</th>
                                    <th class="px-6 py-4">Total Revenue</th>
                                    <th class="px-6 py-4">Avg. Order Value</th>
                                    <th class="px-6 py-4">Orders</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                @forelse($monthlySales as $sale)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                        <td class="px-6 py-4 font-mono font-bold">{{ $sale['month'] }}</td>
                                        <td class="px-6 py-4 text-green-600 dark:text-green-400 font-bold">{{ Number::currency($sale['total_revenue'], 'INR') }}</td>
                                        <td class="px-6 py-4">{{ Number::currency($sale['average_order_value'], 'INR') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 rounded-full text-xs font-bold">
                                                {{ $sale['total_orders'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">No sales data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <!-- 2. Top 10 Products -->
                    <section class="bg-white dark:bg-white/5 dark:backdrop-blur-xl border border-gray-200 dark:border-white/10 rounded-2xl shadow-xl overflow-hidden flex flex-col">
                        <div class="p-6 border-b border-gray-200 dark:border-white/10 flex justify-between items-center bg-gray-50/50 dark:bg-white/5">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🏆</span>
                                <h2 class="text-xl font-bold">Top 10 Products</h2>
                            </div>
                            <a href="{{ route('admin.analytics.export', 'top-products') }}" class="no-transition text-sm font-bold text-blue-600 dark:text-cyan-400 hover:underline">
                                Export CSV
                            </a>
                        </div>
                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left">
                                <thead class="bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-white/60 text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-4">Product</th>
                                        <th class="px-6 py-4 text-right">Qty Sold</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                    @forelse($topProducts as $product)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-gray-900 dark:text-white">{{ $product['product_name'] }}</div>
                                                <div class="text-xs text-gray-400">ID: {{ $product['product_id'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-right font-mono font-bold text-blue-600 dark:text-cyan-400">
                                                {{ number_format($product['total_quantity_sold']) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-6 py-10 text-center text-gray-500 italic">No product data found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- 3. Top 10 Customers -->
                    <section class="bg-white dark:bg-white/5 dark:backdrop-blur-xl border border-gray-200 dark:border-white/10 rounded-2xl shadow-xl overflow-hidden flex flex-col">
                        <div class="p-6 border-b border-gray-200 dark:border-white/10 flex justify-between items-center bg-gray-50/50 dark:bg-white/5">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">💎</span>
                                <h2 class="text-xl font-bold">Top 10 Customers</h2>
                            </div>
                            <a href="{{ route('admin.analytics.export', 'top-customers') }}" class="no-transition text-sm font-bold text-blue-600 dark:text-cyan-400 hover:underline">
                                Export CSV
                            </a>
                        </div>
                        <div class="overflow-x-auto flex-1">
                            <table class="w-full text-left">
                                <thead class="bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-white/60 text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="px-6 py-4">Customer</th>
                                        <th class="px-6 py-4 text-right">Spent</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                    @forelse($topCustomers as $customer)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-gray-900 dark:text-white">{{ $customer['customer_name'] }}</div>
                                                <div class="text-xs text-gray-400">ID: {{ $customer['customer_id'] }} | Orders: {{ $customer['total_orders'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-right font-mono font-bold text-green-600 dark:text-green-400">
                                                {{ Number::currency($customer['total_spent'], 'INR') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-6 py-10 text-center text-gray-500 italic">No customer data found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <!-- 4. Sales by Category -->
                <section class="bg-white dark:bg-white/5 dark:backdrop-blur-xl border border-gray-200 dark:border-white/10 rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-white/10 flex justify-between items-center bg-gray-50/50 dark:bg-white/5">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">🏷️</span>
                            <h2 class="text-xl font-bold">Sales by Category</h2>
                        </div>
                        <a href="{{ route('admin.analytics.export', 'sales-by-category') }}" class="no-transition px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-bold shadow-lg transition transform hover:scale-105 active:scale-95">
                            Export CSV
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-white/60 text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">Category</th>
                                    <th class="px-6 py-4">Revenue</th>
                                    <th class="px-6 py-4">Items Sold</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                @forelse($salesByCategory as $category)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $category['category'] }}</td>
                                        <td class="px-6 py-4 text-green-600 dark:text-green-400 font-bold">{{ Number::currency($category['total_revenue'], 'INR') }}</td>
                                        <td class="px-6 py-4 font-mono">{{ number_format($category['total_items_sold']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center text-gray-500 italic">No category data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
 
            </div>
        </div>
    </div>
</x-app-layout>
