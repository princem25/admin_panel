<div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">

    {{-- Image --}}
    <img src="{{ $product->image ? asset('images/'.$product->image) : 'https://via.placeholder.com/250' }}"
        class="w-full h-44 object-cover">

    {{-- Info --}}
    <div class="p-4">

        <h3 class="text-lg font-semibold mb-1">
            {{ $product->name }}
        </h3>

        <p class="text-sm text-gray-500 line-clamp-2">
            {{ $product->description }}
        </p>

        <p class="font-bold mt-2 text-black">
             @currency($product->price)
        </p>

        {{-- Buttons --}}
        <div class="mt-4 flex items-center gap-2 flex-wrap">

            <a href="{{ route('products.edit', $product->id) }}">
                <button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm">
                    Edit
                </button>
            </a>

            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')

                <button onclick="return confirm('Are you sure?')"
                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                    Delete
                </button>
            </form>

            <a href="{{ route('products.download', $product->id) }}"
                class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 text-sm">
                ⬇ Download
            </a>

        </div>
    </div>
</div>