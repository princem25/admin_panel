@extends('layouts.form')

@section('form')

<h2 class="text-center mb-5 text-xl font-semibold">Edit Product</h2>

<form method="POST" 
      action="{{ route('products.update', $product->id) }}" 
      enctype="multipart/form-data"
      class="max-w-md mx-auto px-12 py-6 bg-white rounded-lg shadow">

    @csrf
    @method('PUT')

    {{-- Name --}}
    <label class="block">Name</label>
    <input type="text" name="name" value="{{ $product->name }}"
        class="w-full p-2 my-2 mb-4 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">

    {{-- Price --}}
    <label class="block">Price</label>
    <input type="number" name="price" value="{{ $product->price }}"
        class="w-full p-2 my-2 mb-4 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-black">

    {{-- Description --}}
    <label class="block">Description</label>
    <textarea name="description" rows="2"
        oninput="this.style.height='auto'; this.style.height=this.scrollHeight+'px';"
        class="w-full p-2 my-2 mb-4 border border-gray-300 rounded resize-none overflow-hidden focus:outline-none focus:ring-2 focus:ring-black">{{ $product->description }}</textarea>

    {{-- Current Image --}}
    @if($product->image)
        <div class="mb-4">
            <p class="text-sm text-gray-600">Current Image:</p>
            <img src="{{ asset('images/'.$product->image) }}" 
                 class="w-32 h-32 object-cover rounded mt-2">
        </div>
    @endif

    {{-- Upload New Image --}}
    <label class="block">Change Image</label>
    <input type="file" name="file"
        class="w-full mb-4 mt-4">

    {{-- Buttons --}}
    <div class="flex justify-between items-center">

        <a href="{{ route('products.index') }}" 
           class="text-gray-600 hover:underline">
            ← Back
        </a>

        <button type="submit"
            class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800">
            Update
        </button>

    </div>

</form>

@endsection