<x-app-layout>

<div id="invoice" class="max-w-3xl mx-auto py-10 px-4 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">

    <!-- Top Actions -->
    <div class="flex justify-between items-center mb-6 print:hidden">
        <h1 class="text-2xl font-bold">Invoice</h1>

        <button onclick="window.print()"
            class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800 
                   dark:bg-white dark:text-black dark:hover:bg-gray-200">
            🖨️ Print
        </button>
    </div>

    <!-- Header Info -->
    <div class="flex justify-between mb-6">
        <div>
            <p class="font-semibold">Invoice</p>
        </div>
        <div class="text-right">
            <p><strong>{{ $invoiceNumber }}</strong></p>
            <p>{{ now()->format('d M Y') }}</p>
        </div>
    </div>

    <!-- Billing Info -->
    <div class="flex justify-between mb-6">
        <div>
            <p class="font-semibold">Billed To:</p>
            <p>{{ $user->name }}</p>
            <p>{{ $user->email }}</p>
        </div>

        <div class="text-right">
            <p class="font-semibold">From:</p>
            <p>{{ config('company.name') }}</p>
            <p>{{ config('company.email') }}</p>
        </div>
    </div>

    <!-- Table -->
    <table class="w-full border border-gray-300 dark:border-gray-700">
        <thead>
            <tr class="bg-gray-100 dark:bg-gray-800">
                <th class="p-2 text-left">Item</th>
                <th class="p-2 text-right">Price</th>
                <th class="p-2 text-center">Qty</th>
                <th class="p-2 text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cartItems as $item)
            <tr class="border-t border-gray-200 dark:border-gray-700">
                <td class="p-2">{{ $item->product->name }}</td>
                <td class="p-2 text-right">₹{{ number_format($item->product->price, 2) }}</td>
                <td class="p-2 text-center">{{ $item->quantity }}</td>
                <td class="p-2 text-right">₹{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <div class="mt-6 text-right">
        <p class="text-lg font-bold">
            Total: ₹{{ number_format($grandTotal, 2) }}
        </p>
    </div>

</div>

</x-app-layout>
 