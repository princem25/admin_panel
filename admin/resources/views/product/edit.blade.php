@extends('layouts.form')

@section('form')

    <h2 class="text-center mb-6 text-2xl font-semibold">Edit Product</h2>

    {{-- Errors --}}
    @if ($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-700 border border-red-300
                    dark:bg-red-500/20 dark:text-red-200 dark:border-red-400/30">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Stock Alert --}}
    @if(isset($product->stock) && $product->stock <= 5 && $product->stock > 0)
        <div class="mb-4 p-3 rounded-xl bg-yellow-50 border border-yellow-300 text-yellow-800
                    dark:bg-yellow-500/10 dark:border-yellow-400/30 dark:text-yellow-300 flex items-center gap-2">
            ⚠️ <strong>Low stock!</strong> Only <strong>{{ $product->stock }}</strong> unit(s) remaining.
        </div>
    @elseif(isset($product->stock) && $product->stock == 0)
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-300 text-red-800
                    dark:bg-red-500/10 dark:border-red-400/30 dark:text-red-300 flex items-center gap-2">
            🚫 <strong>Out of stock!</strong> This product is currently unavailable.
        </div>
    @endif

    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div>
            <label for="name" class="text-sm font-medium text-gray-600 dark:text-white/70">Product Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                class="w-full mt-1 p-2 rounded-lg bg-white border border-gray-300 text-gray-900
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       dark:bg-white/10 dark:border-white/20 dark:text-white dark:focus:ring-cyan-400">
        </div>


        {{-- Category --}}
        <div>
            <label for="category_id" class="text-sm font-medium text-gray-600 dark:text-white/70">Category</label>
            <select id="category_id" name="category_id"
                class="w-full mt-1 p-2 rounded-lg bg-white border border-gray-300 text-gray-900
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       dark:bg-white/10 dark:border-white/20 dark:text-white dark:focus:ring-cyan-400">
                <option value="">Select Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                            class="bg-white text-black dark:bg-gray-800 dark:text-white"
                            {{ $product->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Price + Discount Price --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="price" class="text-sm font-medium text-gray-600 dark:text-white/70">Price (₹)</label>
                <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0"
                    class="w-full mt-1 p-2 rounded-lg bg-white border border-gray-300 text-gray-900
                           focus:outline-none focus:ring-2 focus:ring-blue-500
                           dark:bg-white/10 dark:border-white/20 dark:text-white dark:focus:ring-cyan-400">
            </div>
            <div>
                <label for="discount_price" class="text-sm font-medium text-gray-600 dark:text-white/70">
                    Discount Price (₹) <span class="text-xs text-gray-400">optional</span>
                </label>
                <input type="number" id="discount_price" name="discount_price"
                       value="{{ old('discount_price', $product->discount_price) }}" step="0.01" min="0"
                    class="w-full mt-1 p-2 rounded-lg bg-white border border-gray-300 text-gray-900
                           focus:outline-none focus:ring-2 focus:ring-green-500
                           dark:bg-white/10 dark:border-white/20 dark:text-white dark:focus:ring-green-400">
                @if($product->discount_price)
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                        Current: {{ Number::currency($product->discount_price, 'INR') }}
                        ({{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% off)
                    </p>
                @endif
            </div>
        </div>

        {{-- Stock --}}
        <div>
            <label for="stock" class="text-sm font-medium text-gray-600 dark:text-white/70">Stock Quantity</label>
            <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0"
                class="w-full mt-1 p-2 rounded-lg bg-white border border-gray-300 text-gray-900
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       dark:bg-white/10 dark:border-white/20 dark:text-white dark:focus:ring-cyan-400">
            <p class="text-xs text-gray-400 mt-1">Current stock: <strong>{{ $product->stock ?? 0 }}</strong></p>
        </div>

        {{-- Description --}}
        <div>
            <label for="description" class="text-sm font-medium text-gray-600 dark:text-white/70">Description</label>
            <textarea id="description" name="description" rows="4"
                class="w-full mt-1 p-2 rounded-lg bg-white border border-gray-300 text-gray-900
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       dark:bg-white/10 dark:border-white/20 dark:text-white dark:focus:ring-cyan-400">{{ old('description', $product->description) }}</textarea>
        </div>

        {{-- Current Image --}}
        @if ($product->image)
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-white/60 mb-2">Current Image</p>
                <img src="{{ Storage::url('images/' . $product->image) }}"
                     alt="Current Product Image"
                     class="w-32 h-32 object-cover rounded-lg border border-gray-300 dark:border-white/20 shadow-sm">
            </div>
        @endif

        {{-- Change Image --}}
        <div>
            <label for="file" class="text-sm font-medium text-gray-600 dark:text-white/70">Change Image</label>
            <input type="file" id="file" name="file" accept="image/*"
                class="w-full mt-1 text-sm text-gray-700
                       file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                       file:bg-blue-600 file:text-white hover:file:bg-blue-700
                       dark:text-white dark:file:bg-cyan-500 dark:hover:file:bg-cyan-600 transition-all">
        </div>

        {{-- Buttons --}}
        <div class="flex justify-between items-center mt-8 pt-4 border-t border-gray-200 dark:border-white/10">
            <a href="{{ route('products.index') }}"
               class="text-gray-600 hover:text-gray-900 dark:text-white/70 dark:hover:text-white font-medium transition-colors">
                ← Back to Products
            </a>
            <button type="submit"
                class="px-6 py-2 rounded-lg text-white font-medium
                       bg-blue-600 hover:bg-blue-700
                       dark:bg-cyan-500 dark:hover:bg-cyan-600 shadow-sm hover:shadow-md transition-all">
                Save Changes
            </button>
        </div>

    </form>

@endsection
