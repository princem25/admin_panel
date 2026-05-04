@component('mail::message')
# ⚠️ Low Stock Alert

Hello Admin,

The following products are running low on stock and need to be restocked soon:

@component('mail::table')
| Product | Stock | Category |
| :--- | :--- | :--- |
@foreach ($products as $product)
| **{{ $product->name }}** (#{{ $product->id }}) | {{ $product->stock }} units | {{ $product->category->name ?? 'N/A' }} |
@endforeach
@endcomponent

Please restock these products at your earliest convenience to avoid losing potential sales.

@component('mail::button', ['url' => config('app.url') . '/admin/products'])
Manage Inventory
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
