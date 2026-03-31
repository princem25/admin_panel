@extends('layouts.form')

@section('form')

    <h2 class="text-center mb-6 text-2xl font-semibold">
        Create Product
    </h2>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-100 text-red-700 border border-red-300
                    dark:bg-red-500/20 dark:text-red-200 dark:border-red-400/30">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-5">

        @csrf

        {{-- Name --}}
        <div>
            <label for="name" class="text-sm font-medium text-gray-600 dark:text-white/70">Product Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}"
                class="w-full p-2 mt-1 rounded-lg bg-white border border-gray-300 text-gray-900
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       dark:bg-white/10 dark:border-white/20 dark:text-white dark:focus:ring-cyan-400">
        </div>

        {{-- Category --}}
        <div>
            <label for="category_id" class="text-sm font-medium text-gray-600 dark:text-white/70">Category</label>
            <select id="category_id" name="category_id"
                class="w-full p-2 mt-1 rounded-lg bg-white border border-gray-300 text-gray-900
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       dark:bg-white/10 dark:border-white/20 dark:text-white dark:focus:ring-cyan-400">
                <option value="">Select Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" class="bg-white text-black dark:bg-gray-800 dark:text-white">
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Price + Discount Price side by side --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="price" class="text-sm font-medium text-gray-600 dark:text-white/70">Price (₹)</label>
                <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0"
                    class="w-full p-2 mt-1 rounded-lg bg-white border border-gray-300 text-gray-900
                           focus:outline-none focus:ring-2 focus:ring-blue-500
                           dark:bg-white/10 dark:border-white/20 dark:text-white dark:focus:ring-cyan-400">
            </div>
            <div>
                <label for="discount_price" class="text-sm font-medium text-gray-600 dark:text-white/70">
                    Discount Price (₹) <span class="text-xs text-gray-400">optional</span>
                </label>
                <input type="number" id="discount_price" name="discount_price" value="{{ old('discount_price') }}" step="0.01" min="0"
                    class="w-full p-2 mt-1 rounded-lg bg-white border border-gray-300 text-gray-900
                           focus:outline-none focus:ring-2 focus:ring-green-500
                           dark:bg-white/10 dark:border-white/20 dark:text-white dark:focus:ring-green-400">
                <p class="text-xs text-gray-400 mt-1">Must be less than the actual price.</p>
            </div>
        </div>

        {{-- Stock --}}
        <div>
            <label for="stock" class="text-sm font-medium text-gray-600 dark:text-white/70">Stock Quantity</label>
            <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0"
                class="w-full p-2 mt-1 rounded-lg bg-white border border-gray-300 text-gray-900
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       dark:bg-white/10 dark:border-white/20 dark:text-white dark:focus:ring-cyan-400">
        </div>

        {{-- Description --}}
        <div>
            <label for="description" class="text-sm font-medium text-gray-600 dark:text-white/70">Description</label>
            <textarea id="description" name="description" rows="3"
                class="w-full p-2 mt-1 rounded-lg bg-white border border-gray-300 text-gray-900
                       dark:bg-white/10 dark:border-white/20 dark:text-white">{{ old('description') }}</textarea>
        </div>

        {{-- Image --}}
        <div>
            <label for="file" class="text-sm font-medium text-gray-600 dark:text-white/70">Upload Image</label>
            <input type="file" id="file" name="file" accept="image/*"
                class="w-full mt-1 text-sm text-gray-700
                       file:bg-blue-600 file:text-white file:px-3 file:py-1 file:rounded file:mr-3
                       dark:text-white dark:file:bg-cyan-500">
        </div>

        {{-- Buttons --}}
        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200 dark:border-white/10">
            <a href="{{ route('products.index') }}"
               class="text-gray-600 hover:text-gray-900 dark:text-white/70 dark:hover:text-white font-medium">
                ← Back
            </a>
            <button type="submit"
                class="px-5 py-2 rounded-lg text-white font-medium
                       bg-blue-600 hover:bg-blue-700
                       dark:bg-cyan-500 dark:hover:bg-cyan-600 shadow-sm hover:shadow-md transition-all">
                Create Product
            </button>
        </div>

    </form>

@endsection
