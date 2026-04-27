<x-app-layout>

    <div class="min-h-screen relative overflow-hidden
        bg-gray-50 text-gray-900
        dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white">

        {{-- Glow effects (dark mode only) --}}
        <div class="hidden dark:block absolute w-[500px] h-[500px] bg-cyan-400 opacity-10 blur-3xl rounded-full top-0 left-0 pointer-events-none"></div>
        <div class="hidden dark:block absolute w-[500px] h-[500px] bg-purple-500 opacity-10 blur-3xl rounded-full bottom-0 right-0 pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-6 py-10">

            {{-- Page Header --}}
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold">🌐 API Products</h1>
                    <p class="text-sm text-gray-500 dark:text-white/50 mt-1">Live data from FakeStore API</p>
                </div>
            </div>

            @if($error)
                <div class="mb-6 px-5 py-4 rounded-xl border border-red-300 bg-red-50 dark:bg-red-900/30 dark:border-red-700 text-red-800 dark:text-red-300 flex items-center gap-3 shadow-sm">
                    <span class="text-xl">⚠️</span>
                    <span class="font-medium">{{ $error }}</span>
                </div>
            @endif

            {{-- Filters --}}
            <div class="mb-8 w-[55%]">
                <button type="button" onclick="document.getElementById('filterPanel').classList.toggle('hidden')" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg shadow-sm font-semibold text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/10 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filters
                </button>

                <div id="filterPanel" class="{{ (request('category') && request('category') != 'all') || (request('limit') && request('limit') != 20) ? '' : 'hidden' }} mt-4 p-5 rounded-2xl shadow-sm border bg-white border-gray-200 dark:bg-white/5 dark:border-white/10">
                    <form method="GET" action="{{ route('admin.api-products.index') }}" class="flex flex-col sm:flex-row items-end gap-4">
                        <div class="w-full sm:w-64">
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                            <select name="category" id="category" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                <option value="all">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>
                                        {{ ucfirst($cat) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full sm:w-32">
                            <label for="limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Limit</label>
                            <input type="number" name="limit" id="limit" value="{{ $limit }}" min="1" max="50" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                        </div>

                        <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow font-medium transition">
                            Apply
                        </button>
                        
                        <a href="{{ route('admin.api-products.index') }}" class="w-full sm:w-auto px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white rounded-lg shadow font-medium transition text-center">
                            Reset
                        </a>
                    </form>
                </div>
            </div>

            {{-- Products Grid --}}
            @if(count($products) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="group flex flex-col bg-white border border-gray-200 dark:bg-white/5 dark:border-white/10 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition duration-200">
                            
                            {{-- Image Header --}}
                            <div class="relative w-full aspect-square bg-white flex items-center justify-center overflow-hidden border-b border-gray-200 dark:border-white/10">
                                {{-- Image --}}
                                <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}" class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-300">
                            </div>

                            {{-- Card Body --}}
                            <div class="p-4 flex flex-col flex-grow">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ $product['category'] }}
                                    </span>
                                </div>
                                
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white line-clamp-2 leading-tight mb-2" title="{{ $product['title'] }}">
                                    {{ $product['title'] }}
                                </h3>
                                
                                <p class="text-sm text-gray-600 dark:text-white/60 line-clamp-2 mb-4">
                                    {{ $product['description'] }}
                                </p>
                                
                                <div class="mt-auto">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="font-bold text-lg text-blue-600 dark:text-cyan-400">
                                            ${{ number_format($product['price'], 2) }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-600 dark:text-white/60">
                                            <span class="text-yellow-400 mr-1">★</span> {{ $product['rating']['rate'] ?? 'N/A' }}
                                        </span>
                                    </div>
                                    
                                    <button type="button" onclick="alert('Importing {{ addslashes($product['title']) }} feature coming soon!')" class="w-full py-2 rounded-md text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white transition text-center shadow-sm">
                                        Import to Store
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif(!$error)
                <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <span class="text-4xl block mb-4">📦</span>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">No products found</h3>
                    <p class="text-gray-500 dark:text-gray-400">Try adjusting your filters.</p>
                </div>
            @endif

        </div>
    </div>

</x-app-layout>
