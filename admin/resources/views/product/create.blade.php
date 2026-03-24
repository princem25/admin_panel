@extends('layouts.form')

@section('form')

<h2 class="text-center mb-5 text-xl font-semibold">Create Product</h2>

{{-- 🔴 Global Errors --}}
@if ($errors->any())
    <div class="max-w-md mx-auto mb-4 p-3 bg-red-100 text-red-700 rounded">
        <ul>
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('products.store') }}"  
      enctype="multipart/form-data"
      class="max-w-md mx-auto px-12 py-6 bg-white rounded-lg shadow">
    @csrf

    {{-- Name --}}
    <label class="block">Name</label>
    <input type="text" name="name" value="{{ old('name') }}"
        class="w-full p-2 my-2 mb-1 border rounded focus:outline-none focus:ring-2 
        @error('name') border-red-500 @enderror focus:ring-black">
    
    @error('name')
        <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
    @enderror


    {{-- Price --}}
    <label class="block">Price</label>
    <input type="number" name="price" value="{{ old('price') }}"
        class="w-full p-2 my-2 mb-1 border rounded focus:outline-none focus:ring-2 
        @error('price') border-red-500 @enderror focus:ring-black">
    
    @error('price')
        <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
    @enderror


    {{-- Description --}}
    <label class="block">Description</label>
    <textarea name="description" rows="2"
        oninput="this.style.height='auto'; this.style.height=this.scrollHeight+'px';"
        class="w-full p-2 my-2 mb-1 border rounded resize-none overflow-hidden focus:outline-none focus:ring-2 
        @error('description') border-red-500 @enderror focus:ring-black">{{ old('description') }}</textarea>

    @error('description')
        <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
    @enderror


    {{-- Image --}}
    <label class="block">Upload Image</label>
    <input type="file" name="file"
        class="w-full p-2 my-2 mb-1 border rounded 
        @error('file') border-red-500 @enderror">

    @error('file')
        <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
    @enderror


    {{-- Buttons --}}
    <div class="flex justify-between items-center mt-3">
        <a href="{{ route('products.index') }}" 
           class="text-gray-600 hover:underline">
            ← Back
        </a>

        <button type="submit"
            class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800">
            Save
        </button> 
    </div>

</form>

@endsection