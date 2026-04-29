<x-mail::message>
# Order Confirmation #{{ $order->id }}

Thank you for your order! Here are the details:

<x-mail::table>
| Item       | Quantity | Price  |
| :--------- | :------- | :----- |
@foreach($order->items as $item)
| {{ $item->product->name ?? 'Product' }} | {{ $item->quantity }} | ${{ number_format($item->price, 2) }} |
@endforeach
| **Total**  |          | **${{ number_format($order->total_amount, 2) }}** |
</x-mail::table>

<x-mail::button :url="$url">
View Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
