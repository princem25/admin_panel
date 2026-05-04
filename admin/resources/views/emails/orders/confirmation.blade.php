<x-mail::message>
<div style="text-align: center; margin-bottom: 20px;">
    <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Company Logo" style="max-width: 150px; height: auto;">
</div>

# {{ __('emails.order.greeting', ['name' => $order->user->name ?? 'Customer']) }}

{{ __('emails.order.intro', ['id' => $order->id]) }}

<x-mail::table>
| Item       | Quantity | Price  |
| :--------- | :------- | :----- |
@foreach($order->items as $item)
| {{ $item->product->name ?? 'Product' }} | {{ $item->quantity }} | ${{ number_format($item->price, 2) }} |
@endforeach
| **Total**  |          | **${{ number_format($order->total_amount, 2) }}** |
</x-mail::table>

<x-mail::button :url="$url">
{{ __('emails.order.details_button') }}
</x-mail::button>

{{ __('emails.order.footer') }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
