<x-app-layout>
    <div class="w-[80%] mx-40 px-2 py-6">

        <h1 class="text-blue-500 text-lg mb-4">
            Hello, Admin 👋
        </h1>

       <form action="{{route('products.index')}}">
        <input type="submit" value="Manage Products" class="text-white bg-blue-800 px-3 py-1 cursor-pointer rounded-lg">
       </form>
    
    </div>
</x-app-layout>