<x-app-layout>

    <div class="relative min-h-screen 
        bg-gradient-to-br from-[#0f2027] via-[#203a43] to-[#2c5364] 
        text-white overflow-x-hidden">

        <!-- 🌟 Glow Effects -->
        <div class="absolute w-[400px] h-[400px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10"></div>
        <div class="absolute w-[400px] h-[400px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10"></div>

        <div class="relative w-[80%] mx-auto py-10">

            <!-- Greeting Card -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 
                        p-6 rounded-xl shadow-lg mb-8">

                <h1 class="text-2xl font-semibold text-cyan-400">
                    Hello, {{ Auth::user()->name }} 👋
                </h1>

                <p class="text-white/70 mt-2">
                    Welcome back! Manage your products and cart easily.
                </p>

            </div>

            <!-- Dashboard Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

                <!-- Products -->
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 
                            p-6 rounded-xl shadow-lg hover:scale-105 transition">

                    <div class="text-3xl mb-3">📦</div>

                    <h2 class="text-lg font-semibold mb-1">
                        Products
                    </h2>

                    <p class="text-white/70 text-sm mb-3">
                        Manage your products list
                    </p>

                    <a href="{{ route('user.products') }}"
                       class="text-cyan-400 hover:underline text-sm">
                        View →
                    </a>

                </div>

                <!-- Cart -->
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 
                            p-6 rounded-xl shadow-lg hover:scale-105 transition">

                    <div class="text-3xl mb-3">🛒</div>

                    <h2 class="text-lg font-semibold mb-1">
                        Cart
                    </h2>

                    <p class="text-white/70 text-sm mb-3">
                        View your selected items
                    </p>

                    <a href="{{ route('cart.index') }}"
                       class="text-cyan-400 hover:underline text-sm">
                        Open →
                    </a>

                </div>

                <!-- Preferences -->
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 
                            p-6 rounded-xl shadow-lg hover:scale-105 transition">

                    <div class="text-3xl mb-3">⚙️</div>

                    <h2 class="text-lg font-semibold mb-1">
                        Preferences
                    </h2>

                    <p class="text-white/70 text-sm mb-3">
                        Customize your experience
                    </p>

                    <a href="#"
                       class="text-cyan-400 hover:underline text-sm">
                        Settings →
                    </a>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>