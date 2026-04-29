<x-mail::message>
# Order Status Updated

Hi {{ $order->user->name ?? 'Customer' }},

The status of your order **#{{ $order->id }}** has been updated to: **{{ $status }}**.

@if(strtolower($status) === 'shipped')
Your order is on its way! You will receive it soon.
@elseif(strtolower($status) === 'cancelled')
We're sorry to see this order cancelled. If you have any questions, please contact support.
@endif

<x-mail::button :url="$url">
View Order Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
