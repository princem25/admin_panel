<x-app-layout>

    <div
        class="relative min-h-screen 

    bg-white text-gray-900
    dark:bg-gradient-to-br dark:from-[#0f2027] dark:via-[#203a43] dark:to-[#2c5364] dark:text-white">

        <div class="w-[80%] mx-auto py-10">

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-semibold text-blue-600 dark:text-cyan-400">
                    Product Summary 📊
                </h1>
                <p class="text-gray-600 dark:text-white/70">
                    Category-wise overview of total products
                </p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

                @foreach ($categorySummary as $item)
                    <div
                        class="p-6 rounded-xl 

                    bg-white border border-gray-200 shadow-sm
                    dark:bg-white/10 dark:backdrop-blur-xl dark:border-white/20 dark:shadow-md

                    hover:shadow-md dark:hover:shadow-lg transition">

                        <div class="text-2xl mb-2">📦</div>

                        <h2 class="text-lg font-semibold">
                            {{ $item->name }}
                        </h2>

                        <p class="text-sm mt-1 
                        text-gray-600 dark:text-white/70">
                            {{ $item->total }} products
                        </p>

                    </div>
                @endforeach

            </div>

        </div>
    </div>

</x-app-layout>
