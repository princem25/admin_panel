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
    <div id="flash-error"
         class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50 
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

<body>
    <div class="p-5">

        {{-- Create Button --}}
        <div class="mb-5">
            <h5>{{$greeting}}</h5>
            <a href="{{ route('products.create') }}">
                <button class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800 mt-10">
                    + Create Product
                </button>
            </a>
        </div>

        @if(count($products) > 0)

        <div class="flex flex-wrap gap-5">

            @foreach($products as $product)
            <div class="w-64 border border-gray-200 rounded-lg overflow-hidden shadow">

                {{-- Image --}}
                <img src="{{ $product->image ? asset('images/'.$product->image) : 'https://via.placeholder.com/250' }}"
                    class="w-full h-44 object-cover">

                {{-- Info --}}
                <div class="p-4">

                    <h3 class="mb-2 text-lg font-semibold">
                        {{ $product->name }}
                    </h3>

                    <p class="text-sm text-gray-600">
                        {{ $product->description }}
                    </p>

                    <p class="font-bold mt-2">
                           {{ $product->price}}
                    </p>

                    {{-- Buttons --}}
                    <div class="mt-4 flex gap-2">

                        {{-- Edit --}}
                        <a href="{{ route('products.edit', $product->id) }}">
                            <button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Edit
                            </button>
                        </a>

                        {{-- Delete --}}
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button onclick="return confirm('Are you sure?')"
                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                Delete
                            </button>
                        </form>

                    </div>

                </div>
            </div>
            @endforeach

        </div>

        @else
        <p class="text-gray-700">No products to display</p>
        @endif
         <a href="{{ route('admin.dashboard') }}" 
           class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800 absolute top-10 right-10">
             Back to
            Dashboard
         
        </a> 

    </div>
</body>

</html>