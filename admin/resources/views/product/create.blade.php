<!-- form layout used -->
@extends('layouts.form')

@section('form')

<h2 class="text-center mb-6 text-2xl font-semibold">
    Create Product
</h2>

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

<form method="POST" action="{{ route('products.store') }}"  
      enctype="multipart/form-data"
      class="space-y-4">

    @csrf

    <div>
        <label class="text-white/70 text-sm">Name</label>
        <input type="text" name="name"
            class="w-full p-2 mt-1 rounded-lg bg-white/10 border border-white/20 text-white">
    </div>

    <div>
        <label class="text-white/70 text-sm">Price</label>
        <input type="number" name="price"
            class="w-full p-2 mt-1 rounded-lg bg-white/10 border border-white/20 text-white">
    </div>

    <div>
        <label class="text-white/70 text-sm">Description</label>
        <textarea name="description"
            class="w-full p-2 mt-1 rounded-lg bg-white/10 border border-white/20 text-white"></textarea>
    </div>

    <div>
        <label class="text-white/70 text-sm">Upload Image</label>
        <input type="file" name="file"
            class="w-full mt-1 text-white file:bg-cyan-500 file:text-white file:px-3 file:py-1 file:rounded">
    </div>

    <div class="flex justify-between items-center mt-4">
        <a href="{{ route('products.index') }}" class="text-white/70">
            ← Back
        </a>

        <button type="submit"
            class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg">
            Save
        </button>
    </div>

</form>

@endsection