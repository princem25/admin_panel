<x-app-layout>

    <!-- 🌌 Background -->
    <div
        class="min-h-screen relative overflow-hidden 

    bg-white text-gray-900
    dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white">

        <!-- Glow Effects (only dark) -->
        <div
            class="hidden dark:block absolute w-[500px] h-[500px] bg-cyan-400 opacity-20 blur-3xl rounded-full top-10 left-10">
        </div>
        <div
            class="hidden dark:block absolute w-[500px] h-[500px] bg-blue-500 opacity-20 blur-3xl rounded-full bottom-10 right-10">
        </div>

        <div class="relative max-w-5xl mx-auto px-6 py-10">

            <!-- Header -->
            <h1 class="text-3xl font-bold mb-8">
                Hello, Admin 👋
            </h1>

            <!-- 🚀 Action Card -->
            <div
                class="rounded-xl p-6 mb-6 flex items-center justify-between transition shadow

            bg-white border border-gray-200 hover:scale-[1.02]
            dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-lg">

                <div>
                    <h2 class="text-lg font-semibold">
                        Product Management
                    </h2>
                    <p class="text-gray-600 dark:text-white/70 text-sm">
                        Create, edit and manage your products
                    </p>
                </div>

                <form action="{{ route('products.index') }}">
                    <button type="submit"
                        class="px-4 py-2 rounded-lg transition shadow text-white

                        bg-blue-600 hover:bg-blue-700
                        dark:bg-cyan-500 dark:hover:bg-cyan-600">
                        Manage Products →
                    </button>
                </form>

            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

                @foreach ($categorySummary as $item)
                    <div
                        class="p-5 rounded-xl transition

        bg-white border border-gray-200 shadow-sm
        dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md

        hover:shadow-md dark:hover:shadow-lg">

                        <!-- Icon -->
                        <div class="text-2xl mb-3">📦</div>

                        <!-- Category Name -->
                        <h2 class="text-lg font-semibold">
                            {{ $item->name }}
                        </h2>

                        <!-- Product Count -->
                        <p class="text-sm mt-1 
            text-gray-600 dark:text-white/70">
                            {{ $item->total }} products
                        </p>

                    </div>
                @endforeach

            </div>

            <!-- 🏢 Company Info -->
            <div
                class="rounded-xl p-6 shadow mt-6

            bg-white border border-gray-200
            dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-lg">

                <h2 class="text-lg font-semibold mb-4">
                    Company Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="p-3 rounded-lg 
                    bg-gray-100 
                    dark:bg-white/5">
                        <p class="text-sm text-gray-500 dark:text-white/60">Name</p>
                        <p class="font-medium">{{ config('company.name') }}</p>
                    </div>

                    <div class="p-3 rounded-lg 
                    bg-gray-100 
                    dark:bg-white/5">
                        <p class="text-sm text-gray-500 dark:text-white/60">Email</p>
                        <p class="font-medium">{{ config('company.email') }}</p>
                    </div>

                    <div class="p-3 rounded-lg 
                    bg-gray-100 
                    dark:bg-white/5">
                        <p class="text-sm text-gray-500 dark:text-white/60">City</p>
                        <p class="font-medium">{{ config('company.address.city') }}</p>
                    </div>

                    <div class="p-3 rounded-lg 
                    bg-gray-100 
                    dark:bg-white/5">
                        <p class="text-sm text-gray-500 dark:text-white/60">Tax</p>
                        <p class="font-medium text-green-600 dark:text-green-400">
                            {{ config('company.tax') }}%
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
