<div
    class="bg-white/5 rounded-xl overflow-hidden shadow-md hover:shadow-xl hover:-translate-y-1 transition duration-300">

    {{-- Image --}}
    <img src="{{ $product->image ? asset('images/' . $product->image) : 'https://via.placeholder.com/250' }}"
        class="w-full h-44 object-cover">

    {{-- Info --}}
    <div class="p-4 flex flex-col justify-between h-[160px]">

        <div>
            <h3 class="text-lg font-semibold text-white mb-1">
                {{ $product->name }}
            </h3>

            <p class="text-sm text-white/60 line-clamp-2">
                {{ $product->description }}
            </p>

            <p class="font-bold mt-2 text-cyan-400">
                @currency($product->price)
            </p>
        </div>

        {{-- Buttons --}}
        <div class="mt-4 flex gap-2">

            <!-- Edit -->
            <a href="{{ route('products.edit', $product->id) }}" class="flex-1 text-center py-1 rounded-md text-sm 
               bg-blue-500 hover:bg-blue-600 text-white transition">
                Edit
            </a>

            <!-- Delete -->
            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="flex-1">
                @csrf
                @method('DELETE')

                <button onclick="return confirm('Are you sure?')" class="w-full py-1 rounded-md text-sm 
                    bg-red-500 hover:bg-red-600 text-white transition">
                    Delete
                </button>
            </form>

            <!-- Download -->
            <a href="{{ url('products/' . $product->id . '/download') }}" class="flex-1 text-center py-1 rounded-md text-sm 
               bg-emerald-500 hover:bg-emerald-600 text-white transition">
                ⬇
            </a>

        </div>
    </div>
</div>