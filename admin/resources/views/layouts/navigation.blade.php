<nav x-data="{ open: false }"
    class="sticky top-0 z-50 shadow-md border-b 
        bg-white/80 backdrop-blur-md border-gray-200
        dark:bg-gradient-to-r dark:from-[#355c63]/80 dark:to-[#2f4f54]/80 dark:backdrop-blur-xl dark:border-white/10">

    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between h-16 items-center">

            <!-- Left -->
            <div class="flex items-center space-x-6">

                <div class="hidden sm:flex space-x-6">

                    <a href="{{ $current_logged_user->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                        class="transition py-2 px-1 border-b-2 {{ request()->routeIs('admin.dashboard', 'dashboard') ? 'text-gray-900 border-blue-600 font-bold dark:text-white dark:border-cyan-400' : 'text-gray-600 border-transparent hover:text-gray-900 dark:text-white/80 dark:hover:text-white' }}">
                        Dashboard
                    </a>

                    <a href="{{ $current_logged_user->role === 'admin' ? route('products.index') : route('user.products') }}"
                        class="transition py-2 px-1 border-b-2 {{ request()->routeIs('products.*', 'user.products*') ? 'text-gray-900 border-blue-600 font-bold dark:text-white dark:border-cyan-400' : 'text-gray-600 border-transparent hover:text-gray-900 dark:text-white/80 dark:hover:text-white' }}">
                        Products
                    </a>

                    <a href="{{ $current_logged_user->role === 'admin' ? route('admin.orders.index') : route('orders.index') }}"
                        class="transition py-2 px-1 border-b-2 {{ request()->routeIs('admin.orders.*', 'orders.*') ? 'text-gray-900 border-blue-600 font-bold dark:text-white dark:border-cyan-400' : 'text-gray-600 border-transparent hover:text-gray-900 dark:text-white/80 dark:hover:text-white' }}">
                        Orders
                    </a>

                    @if ($current_logged_user->role === 'admin')
                        <a href="{{ route('admin.users.index') }}"
                            class="transition py-2 px-1 border-b-2 {{ request()->routeIs('admin.users.*') ? 'text-gray-900 border-blue-600 font-bold dark:text-white dark:border-cyan-400' : 'text-gray-600 border-transparent hover:text-gray-900 dark:text-white/80 dark:hover:text-white' }}">
                            Customers
                        </a>

                        <a href="{{ route('admin.invoices.index') }}"
                            class="transition py-2 px-1 border-b-2 {{ request()->routeIs('admin.invoices.*') ? 'text-gray-900 border-blue-600 font-bold dark:text-white dark:border-cyan-400' : 'text-gray-600 border-transparent hover:text-gray-900 dark:text-white/80 dark:hover:text-white' }}">
                            Invoices
                        </a>

                        <a href="{{ route('admin.analytics.index') }}"
                            class="transition py-2 px-1 border-b-2 {{ request()->routeIs('admin.analytics.*') ? 'text-gray-900 border-blue-600 font-bold dark:text-white dark:border-cyan-400' : 'text-gray-600 border-transparent hover:text-gray-900 dark:text-white/80 dark:hover:text-white' }}">
                            Sales Analytics
                        </a>

                        <a href="{{ route('admin.reports.index') }}"
                            class="transition py-2 px-1 border-b-2 {{ request()->routeIs('admin.reports.*') ? 'text-gray-900 border-blue-600 font-bold dark:text-white dark:border-cyan-400' : 'text-gray-600 border-transparent hover:text-gray-900 dark:text-white/80 dark:hover:text-white' }}">
                            Reports
                        </a>

                        <a href="{{ route('admin.api-products.index') }}"
                            class="transition py-2 px-1 border-b-2 {{ request()->routeIs('admin.api-products.*') ? 'text-gray-900 border-blue-600 font-bold dark:text-white dark:border-cyan-400' : 'text-gray-600 border-transparent hover:text-gray-900 dark:text-white/80 dark:hover:text-white' }}">
                            API Products
                        </a>
                    @endif

                </div>
            </div>

            <!-- Right -->
            <div class="flex items-center space-x-6">

                {{-- Cart --}}
                @if ($current_logged_user->role !== 'admin')
                    <a href="{{ route('cart.index') }}"
                        class="relative transition 
                            text-gray-600 hover:text-gray-900
                            dark:text-white/80 dark:hover:text-white"
                        title="{{ trans_choice('cart_items', $cartCount ?? 0, ['count' => $cartCount ?? 0]) }}">

                        <!-- Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 4h12m-10 0a1 1 0 102 0m6 0a1 1 0 102 0" />
                        </svg>

                        {{-- Cart Count --}}
                        @if (isset($cartCount) && $cartCount > 0)
                            <span
                                class="absolute -top-2 -right-2 text-xs rounded-full px-1.5 font-bold
                                    bg-blue-600 text-white
                                    dark:bg-white dark:text-teal-700">
                                {{ $cartCount }}
                            </span>
                        @endif

                    </a>
                @endif

                <!-- User Dropdown -->
                <div class="relative" x-data="{ dropdown: false }">

                    <button @click="dropdown = !dropdown"
                        class="flex items-center space-x-2 transition
                            text-gray-600 hover:text-gray-900
                            dark:text-white/80 dark:hover:text-white">
                        <span>{{ Auth::user()->name }}</span>

                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="dropdown" @click.outside="dropdown = false" x-transition x-cloak
                        class="absolute right-0 mt-2 w-44 rounded-lg shadow-lg
                            bg-white border border-gray-200
                            dark:bg-[#2f4f54] dark:border-white/10">

                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 transition
                                text-gray-700 hover:bg-gray-100
                                dark:text-white/80 dark:hover:bg-white/10">
                            Profile
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                class="w-full text-left px-4 py-2 transition
                                    text-red-600 hover:bg-gray-100
                                    dark:text-red-300 dark:hover:bg-white/10">
                                Logout
                            </button>
                        </form>

                    </div>

                </div>

            </div>

        </div>
    </div>

</nav>
