{{-- ✅ Success Message --}}
@if(session('success'))
    <div id="flash-success"
        class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50 
             max-w-sm w-full text-center p-3 bg-green-100 text-green-700 rounded shadow-lg transition-opacity duration-500">
        {{ session('success') }}
    </div>

    <script>
        setTimeout(() => {
            const el = document.getElementById('flash-success');
            if (el) {
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }
        }, 3000);
    </script>
@endif


{{-- ❌ Error Message --}}
@if(session('error'))
    <div id="flash-error" class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50 
             max-w-sm w-full text-center p-3 bg-red-100 text-red-700 rounded shadow-lg transition-opacity duration-500">
        {{ session('error') }}
    </div>

    <script>
        setTimeout(() => {
            const el = document.getElementById('flash-error');
            if (el) {
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }
        }, 3000);
    </script>
@endif

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100">

<div class="max-w-7xl mx-auto p-6">

    {{-- 🔝 Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">{{$greeting}}</h2>

        <a href="{{ route('products.create') }}">
            <button class="px-5 py-2 bg-black text-white rounded-lg shadow hover:bg-gray-800 transition">
                + Create Product
            </button>
        </a>
    </div>

    {{-- 🔍 Search Box --}}
    <form method="GET" action="{{ route('products.index') }}"
        class="mb-8 flex flex-wrap gap-4 items-end bg-white p-5 rounded-xl shadow-md w-[75%]">

        <div>
            <label class="text-sm text-gray-600">Product Name</label>
            <input type="text" name="search" value="{{ request('search') }}"
                class="border p-2 rounded-lg w-44 focus:ring-2 focus:ring-black outline-none">
        </div>

        <div>
            <label class="text-sm text-gray-600">Category</label>
            <select name="category"
                class="border p-2 rounded-lg w-44 focus:ring-2 focus:ring-black outline-none">
                <option value="">All</option>
                <option value="electronics" {{ request('category')=='electronics'?'selected':'' }}>Electronics</option>
                <option value="fashion" {{ request('category')=='fashion'?'selected':'' }}>Fashion</option>
                <option value="books" {{ request('category')=='books'?'selected':'' }}>Books</option>
            </select>
        </div>

        <div>
            <label class="text-sm text-gray-600">Price</label>
            <input type="number" name="price" value="{{ request('price') }}"
                class="border p-2 rounded-lg w-32 focus:ring-2 focus:ring-black outline-none">
        </div>

        <div class="flex gap-2">
            <button type="submit"
                class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition">
                Search
            </button>

            <a href="{{ route('products.index') }}"
                class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition">
                Reset
            </a>
        </div>
    </form>

    {{-- 📦 Product Grid --}}
    @if(count($products) > 0)

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

        @foreach($products as $product)
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
                    ₹{{ $product->price }}
                </p>

                {{-- Buttons --}}
                <div class="mt-4 flex justify-between">

                    <a href="{{ route('products.edit', $product->id) }}">
                        <button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                            Edit
                        </button>
                    </a>

                    <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button onclick="return confirm('Are you sure?')"
                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                            Delete
                        </button>
                    </form>

                </div>
            </div>
        </div>
        @endforeach

    </div>

    @else
        <div class="text-center text-gray-500 mt-10">
            No products found
        </div>
    @endif

    {{-- 🔙 Back Button --}}
    <div class="mt-10 text-right">
        <a href="{{ route('admin.dashboard') }}"
            class="px-5 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition">
            ← Dashboard
        </a>
    </div>

</div>

</body>

</html>