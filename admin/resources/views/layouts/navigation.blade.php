<nav x-data="{ open: false }" class="relative z-50 bg-white/80 dark:bg-gray-900 backdrop-blur-xl border-b shadow">

    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between h-16 items-center">

            <!-- Logo + Links -->
            <div class="flex items-center space-x-6 ">

                <!-- Desktop Links -->
                <div class="hidden sm:flex space-x-6">
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                        class="text-gray-700 dark:text-gray-300 hover:text-blue-500 transition">
                        Dashboard
                    </a>

                    <a href="{{ auth()->user()->role === 'admin' ? route('products.index') : route('user.products') }}"
                        class="text-gray-700 dark:text-gray-300 hover:text-blue-500 transition">
                        Products
                    </a>
                </div>
            </div>
            @if(auth()->user()->role !== 'admin')
                <a href="{{ route('cart.index') }}"
                    class="absolute right-10 text-gray-700 dark:text-gray-300 hover:text-blue-500">

                    <!-- Cart Icon (Heroicons) -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 4h12m-10 0a1 1 0 102 0m6 0a1 1 0 102 0" />
                    </svg>

                    <!-- Cart Count Badge -->
                    @php
                        $cart = session('cart', []);
                        $count = count($cart);
                    @endphp

                    @if($count > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs  rounded-full px-1.5">
                            {{ $count }}
                        </span>
                    @endif

                </a>
            @endif
            <!-- User Dropdown -->
            <div class="hidden sm:flex items-center">

                <div class="relative" x-data="{ dropdown: false }">

                    <!-- Button -->
                    <button @click="dropdown = !dropdown"
                        class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-blue-500">

                        <span>{{ Auth::user()->name }}</span>

                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="dropdown" @click.outside="dropdown = false" x-transition x-cloak class="absolute right-0 mt-2 w-44 
                        bg-white dark:bg-gray-800 
                        border rounded-lg shadow-lg overflow-hidden z-[999]">

                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Profile
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-red-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Logout
                            </button>
                        </form>

                    </div>

                </div>

            </div>

            <!-- Mobile Button -->
            <div class="sm:hidden">
                <button @click="open = !open" class="text-gray-700 dark:text-gray-300 text-xl">
                    ☰
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" x-transition class="sm:hidden px-6 pb-4 space-y-2">

        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
            class="block text-gray-700 dark:text-gray-300 hover:text-blue-500">
            Dashboard
        </a>

        <a href="{{ auth()->user()->role === 'admin' ? route('products.index') : route('user.products') }}"
            class="block text-gray-700 dark:text-gray-300 hover:text-blue-500">
            Products
        </a>

        <div class="border-t pt-3 mt-3">
            <div class="text-gray-500 text-sm">{{ Auth::user()->email }}</div>

            <a href="{{ route('profile.edit') }}" class="block mt-2 text-gray-700 dark:text-gray-300">
                Profile
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="block mt-2 text-red-500">
                    Logout
                </button>
            </form>
        </div>

    </div>

</nav>