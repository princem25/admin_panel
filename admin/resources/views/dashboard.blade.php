<x-app-layout>

    <div class="relative min-h-screen overflow-x-hidden 

    bg-white text-gray-900
    dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white">

        <!-- 🌟 Glow Effects (dark only) -->
        <div class="hidden dark:block absolute w-[400px] h-[400px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10"></div>
        <div class="hidden dark:block absolute w-[400px] h-[400px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10"></div>

        <div class="relative w-[80%] mx-auto py-10">

            <!-- Greeting Card -->
            <div class="p-6 rounded-xl mb-8

            bg-white border border-gray-200 shadow-sm
            dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-lg">

                <h1 class="text-2xl font-semibold 
                text-blue-600 dark:text-cyan-400">
                    Hello, {{ Auth::user()->name }} 👋
                </h1>

                <p class="mt-2 
                text-gray-600 dark:text-white/70">
                    Welcome back! Manage your products and cart easily.
                </p>

            </div>

            <!-- Dashboard Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

                <!-- Products -->
                <div class="p-6 rounded-xl transition duration-200

                bg-white border border-gray-200 shadow-sm
                dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md

                hover:shadow-md dark:hover:shadow-lg">

                    <div class="text-3xl mb-3">📦</div>

                    <h2 class="text-lg font-semibold mb-1">
                        Products
                    </h2>

                    <p class="text-sm mb-3 
                    text-gray-600 dark:text-white/70">
                        Manage your products list
                    </p>

                    <a href="{{ route('user.products') }}"
                       class="text-sm 

                       text-blue-600 hover:underline
                       dark:text-cyan-400">
                        View →
                    </a>

                </div>

                <!-- Cart -->
                <div class="p-6 rounded-xl transition duration-200

                bg-white border border-gray-200 shadow-sm
                dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md

                hover:shadow-md dark:hover:shadow-lg">

                    <div class="text-3xl mb-3">🛒</div>

                    <h2 class="text-lg font-semibold mb-1">
                        Cart
                    </h2>

                    <p class="text-sm mb-3 
                    text-gray-600 dark:text-white/70">
                        View your selected items
                    </p>

                    <a href="{{ route('cart.index') }}"
                       class="text-sm 

                       text-blue-600 hover:underline
                       dark:text-cyan-400">
                        Open →
                    </a>

                </div>

                <!-- Preferences -->
                <div class="p-6 rounded-xl transition duration-200

                bg-white border border-gray-200 shadow-sm
                dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md

                hover:shadow-md dark:hover:shadow-lg">

                    <div class="text-3xl mb-3">⚙️</div>

                    <h2 class="text-lg font-semibold mb-1">
                        Preferences
                    </h2>

                    <p class="text-sm mb-3 
                    text-gray-600 dark:text-white/70">
                        Customize your experience
                    </p>

                    <a href="#"
                       class="text-sm 

                       text-blue-600 hover:underline
                       dark:text-cyan-400">
                        Settings →
                    </a>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>