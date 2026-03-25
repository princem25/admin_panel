<nav x-data="{ open: false }"
     class="relative z-50 shadow-md border-b 

     bg-white border-gray-200
     dark:bg-gradient-to-r dark:from-[#355c63] dark:to-[#2f4f54] dark:border-white/10">

    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between h-16 items-center">

            <!-- Left -->
            <div class="flex items-center space-x-6">

                <h1 class="font-semibold text-lg 
                text-gray-900 dark:text-white">
                    Product
                </h1>

                <div class="hidden sm:flex space-x-6">

                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                       class="transition 

                       text-gray-600 hover:text-gray-900
                       dark:text-white/80 dark:hover:text-white">
                        Dashboard
                    </a>

                    <a href="{{ auth()->user()->role === 'admin' ? route('products.index') : route('user.products') }}"
                       class="transition 

                       text-gray-600 hover:text-gray-900
                       dark:text-white/80 dark:hover:text-white">
                        Products
                    </a>

                </div>
            </div>

            <!-- Right -->
            <div class="flex items-center space-x-6">

                <!-- Cart -->
                @if(auth()->user()->role !== 'admin')
                    <a href="{{ route('cart.index') }}"
                       class="relative transition 

                       text-gray-600 hover:text-gray-900
                       dark:text-white/80 dark:hover:text-white">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 4h12m-10 0a1 1 0 102 0m6 0a1 1 0 102 0" />
                        </svg>

                        @php $count = count(session('cart', [])); @endphp

                        @if($count > 0)
                            <span class="absolute -top-2 -right-2 text-xs rounded-full px-1.5 font-bold

                            bg-blue-600 text-white
                            dark:bg-white dark:text-teal-700">
                                {{ $count }}
                            </span>
                        @endif
                    </a>
                @endif

                <!-- User -->
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
                    <div x-show="dropdown"
                        @click.outside="dropdown = false"
                        x-transition x-cloak
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
                            <button class="w-full text-left px-4 py-2 transition

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