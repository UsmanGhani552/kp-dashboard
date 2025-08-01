@component('mail::message')
New Order with Order # {{ $order->order_id }} has been created

@component('mail::table')
| Column           | Value
| -------------    | -------------:
| Customer         | {{ $order->customer->name }}
| Created by       | {{ $order->user->name }}
| Client Of        | {{ ucwords($order->client_of) }}
| Order Amount     | ${{ $order->item_price }}
| Remaining Amount | ${{ $order->remaining_price }}
| Total Amount     | ${{ $order->total_price }}
@endcomponent

@component('mail::button', ['url' => route('links.show', [$order->id]) ])
View in Dashboard
@endcomponent

Thanks,<br>
Koderspedia
@endcomponent
