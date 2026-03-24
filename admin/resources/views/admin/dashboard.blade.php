<x-app-layout>
    <div class="max-w-5xl mx-auto px-6 py-10">

        {{-- Header --}}
        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            Hello, Admin 👋
        </h1>

        {{-- Action Card --}}
        <div class="bg-white shadow-md rounded-xl p-6 mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Product Management</h2>
                <p class="text-sm text-gray-500">Create, edit and manage your products</p>
            </div>

            <form action="{{ route('products.index') }}">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 shadow">
                    Manage Products →
                </button>
            </form>
        </div>

        {{-- Company Info Card --}}
        <div class="bg-white shadow-md rounded-xl p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                Company Information
            </h2>

            <div class="grid grid-cols-2 gap-4 text-gray-600">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-500">Name</p>
                    <p class="font-medium">{{ config('company.name') }}</p>
                </div>

                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium">{{ config('company.email') }}</p>
                </div>

                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-500">City</p>
                    <p class="font-medium">{{ config('company.address.city') }}</p>
                </div>

                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-500">Tax</p>
                    <p class="font-medium text-green-600">
                        {{ config('company.tax') }}%
                    </p>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>