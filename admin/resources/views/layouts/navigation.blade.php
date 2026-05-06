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
                {{-- Language Switcher --}}
                <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-white/40">
                    <a href="{{ route('language.switch', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'text-blue-600 dark:text-cyan-400' : 'hover:text-gray-900 dark:hover:text-white' }} transition">EN</a>
                    <span>|</span>
                    <a href="{{ route('language.switch', 'ar') }}" class="{{ app()->getLocale() == 'ar' ? 'text-blue-600 dark:text-cyan-400' : 'hover:text-gray-900 dark:hover:text-white' }} transition">AR</a>
                </div>

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

                {{-- Notifications Bell --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="relative transition 
                            text-gray-600 hover:text-gray-900
                            dark:text-white/80 dark:hover:text-white">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>

                        <span id="notifications-count" class="notifications-count absolute -top-1 -right-1 text-[10px] rounded-full px-1 font-bold bg-red-600 text-white border-2 border-white dark:border-[#2f4f54] {{ (isset($unreadNotificationsCount) && $unreadNotificationsCount > 0) ? '' : 'hidden' }}">
                            {{ $unreadNotificationsCount ?? 0 }}
                        </span>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition x-cloak
                        class="absolute right-0 mt-2 w-80 rounded-2xl shadow-2xl bg-white border border-gray-200 dark:bg-[#2f4f54] dark:border-white/10 overflow-hidden">
                        
                        <div class="p-4 border-b border-gray-100 dark:border-white/5 flex justify-between items-center bg-gray-50 dark:bg-white/5">
                            <h3 class="text-sm font-black text-gray-800 dark:text-white uppercase tracking-widest">Notifications</h3>
                            @if (isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                                <button onclick="markAllAsRead()" class="text-[10px] font-bold text-blue-600 dark:text-cyan-400 hover:underline">Mark all as read</button>
                            @endif
                        </div>

                        <div class="max-h-96 overflow-y-auto">
                            @forelse ($recentNotifications as $notification)
                                <div class="p-4 border-b border-gray-50 dark:border-white/5 last:border-0 hover:bg-gray-50 dark:hover:bg-white/5 transition {{ $notification->unread() ? 'bg-blue-50/50 dark:bg-blue-500/5' : '' }}">
                                    <div class="flex gap-3">
                                        <div class="mt-1">
                                            @php $icon = $notification->data['icon'] ?? 'bell'; @endphp
                                            <span class="text-xl">
                                                @if($icon == 'truck') 🚚 @elseif($icon == 'package') 📦 @else 🔔 @endif
                                            </span>
                                        </div>
                                        <div class="flex-1 cursor-pointer" onclick="markAsRead('{{ $notification->id }}')">
                                            <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $notification->data['message'] ?? 'New Notification' }}</p>
                                            <p class="text-[10px] text-gray-500 mt-1 uppercase">{{ $notification->created_at->diffForHumans() }}</p>
                                            @if (isset($notification->data['url']))
                                                <span class="mt-2 block text-xs font-bold text-blue-600 dark:text-cyan-400 hover:underline">View Details</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <p class="text-gray-400 text-sm italic">No notifications yet.</p>
                                </div>
                            @endforelse
                        </div>

                        <a href="{{ route('notifications.index') }}" class="block p-4 text-center text-xs font-bold text-gray-500 dark:text-white/40 hover:text-gray-900 dark:hover:text-white transition bg-gray-50 dark:bg-white/5 border-t border-gray-100 dark:border-white/5 uppercase tracking-widest">
                            View All Notifications
                        </a>
                    </div>
                </div>

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

<script>
    function markAsRead(id) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.reload();
            }
        });
    }

    function markAllAsRead() {
        fetch(`/notifications/read-all`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }
</script>
