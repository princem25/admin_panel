<x-app-layout>

    <!-- 🌌 Background -->
    <div class="min-h-screen 
    bg-gradient-to-br from-[#0f2027] via-[#203a43] to-[#2c5364] text-white relative overflow-hidden">

        <!-- Glow Effects -->
        <div class="absolute w-[500px] h-[500px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10"></div>
        <div class="absolute w-[500px] h-[500px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10"></div>

        <div class="relative max-w-5xl mx-auto px-6 py-10">

            <!-- Header -->
            <h1 class="text-3xl font-bold mb-8">
                Hello, Admin 👋
            </h1>

            <!-- 🚀 Action Card -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 
            rounded-xl p-6 mb-6 flex items-center justify-between shadow-lg hover:scale-[1.02] transition">

                <div>
                    <h2 class="text-lg font-semibold">
                        Product Management
                    </h2>
                    <p class="text-white/70 text-sm">
                        Create, edit and manage your products
                    </p>
                </div>

                <form action="{{ route('products.index') }}">
                    <button type="submit"
                        class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg transition shadow">
                        Manage Products →
                    </button>
                </form>

            </div>

            <!-- 🏢 Company Info -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 
            rounded-xl p-6 shadow-lg">

                <h2 class="text-lg font-semibold mb-4">
                    Company Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="bg-white/5 p-3 rounded-lg">
                        <p class="text-sm text-white/60">Name</p>
                        <p class="font-medium">{{ config('company.name') }}</p>
                    </div>

                    <div class="bg-white/5 p-3 rounded-lg">
                        <p class="text-sm text-white/60">Email</p>
                        <p class="font-medium">{{ config('company.email') }}</p>
                    </div>

                    <div class="bg-white/5 p-3 rounded-lg">
                        <p class="text-sm text-white/60">City</p>
                        <p class="font-medium">{{ config('company.address.city') }}</p>
                    </div>

                    <div class="bg-white/5 p-3 rounded-lg">
                        <p class="text-sm text-white/60">Tax</p>
                        <p class="font-medium text-green-400">
                            {{ config('company.tax') }}%
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>