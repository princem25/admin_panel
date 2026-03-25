<!-- form layout used -->
@extends('layouts.form')
@section('form')

<h2 class="text-center mb-6 text-2xl font-semibold">
    Edit Product
</h2>

{{-- 🔴 Errors --}}
@if ($errors->any())
    <div class="mb-4 p-3 rounded-xl 
    bg-red-500/20 text-red-200 border border-red-400/30">
        <ul>
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" 
      action="{{ route('products.update', $product->id) }}" 
      enctype="multipart/form-data"
      class="space-y-4">

    @csrf
    @method('PUT')

    {{-- Name --}}
    <div>
        <label class="text-sm text-white/70">Name</label>
        <input type="text" name="name" value="{{ old('name', $product->name) }}"
            class="w-full p-2 mt-1 rounded-lg 
            bg-white/10 border border-white/20 text-white 
            focus:outline-none focus:ring-2 focus:ring-cyan-400">
    </div>

    {{-- Price --}}
    <div>
        <label class="text-sm text-white/70">Price</label>
        <input type="number" name="price" value="{{ old('price', $product->price) }}"
            class="w-full p-2 mt-1 rounded-lg 
            bg-white/10 border border-white/20 text-white 
            focus:outline-none focus:ring-2 focus:ring-cyan-400">
    </div>

    {{-- Description --}}
    <div>
        <label class="text-sm text-white/70">Description</label>
        <textarea name="description"
            class="w-full p-2 mt-1 rounded-lg 
            bg-white/10 border border-white/20 text-white">{{ old('description', $product->description) }}</textarea>
    </div>

    {{-- Image --}}
    @if($product->image)
        <div>
            <p class="text-sm text-white/60">Current Image:</p>
            <img src="{{ asset('images/'.$product->image) }}" 
                 class="w-32 h-32 object-cover rounded-lg mt-2 border border-white/20">
        </div>
    @endif

    <div>
        <label class="text-sm text-white/70">Change Image</label>
        <input type="file" name="file"
            class="w-full mt-1 text-sm text-white file:bg-cyan-500 file:text-white file:px-3 file:py-1 file:rounded">
    </div>

    {{-- Buttons --}}
    <div class="flex justify-between items-center mt-4">

        <a href="{{ route('products.index') }}" 
           class="text-white/70 hover:text-white">
            ← Back
        </a>

        <button type="submit"
            class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg">
            Update
        </button>

    </div>

</form>

@endsection