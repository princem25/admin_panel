<div
    class="rounded-xl overflow-hidden shadow-sm transition duration-200
    bg-white border border-gray-200
    dark:bg-white/5 dark:border-white/10 dark:shadow-md
    hover:shadow-md dark:hover:shadow-lg
    h-[400px] flex flex-col">

    {{-- Image --}}
    <img src="{{ ($product->image && $product->image !== 'default.jpg') ? Storage::url('images/' . $product->image) : 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=250&h=250&fit=crop' }}"
        class="w-full h-44 object-cover"
        onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';">

    {{-- Info --}}
    <div class="p-4 flex flex-col justify-between flex-1">

        <div>
            <h3 class="text-lg font-semibold mb-1 text-gray-900 dark:text-white line-clamp-1">
                {{ $product->name }}
            </h3>
 
            <p class="text-sm line-clamp-2 
            text-gray-600 dark:text-white/60">
                {{ $product->description }}
            </p>

            <p class="font-bold mt-2 
            text-blue-600 dark:text-cyan-400">
                {{ Number::currency($product->price, 'INR') }}
            </p>
        </div>

        {{-- Buttons --}}
        <div class="mt-4 flex gap-2">

            <!-- Edit -->
            <a href="{{ route('products.edit', $product) }}" 
               class="flex-1 text-center py-1 rounded-md text-sm 
               bg-blue-600 hover:bg-blue-700 text-white transition">
                {{ __('products.edit_product') }}
            </a>

            <!-- Delete -->
            <form action="{{ route('products.destroy', $product) }}" method="POST" class="flex-1">
                @csrf
                @method('DELETE')

                <button onclick="return confirm('{{ __('products.delete_confirm') }}')" 
                    class="w-full py-1 rounded-md text-sm 
                    bg-red-500 hover:bg-red-600 text-white transition">
                    Delete
                </button>
            </form>

            <!-- Download -->
            <a href="{{ route('admin.products.download', $product) }}" 
               class="no-transition flex-1 text-center py-1 rounded-md text-sm 
               bg-emerald-500 hover:bg-emerald-600 text-white transition">
                ⬇
            </a>

        </div>
    </div>
</div>