@component('mail::message', [
    'app_name' => ucwords($order->brand->name),
    'img_path' => "images/brands" . $order->brand->logo,
    'img_alt' => ucfirst($order->brand->name) . " Logo"
])
Order # {{ $order->id }} has been paid

@component('mail::table')
| Column           | Value
| -------------    | -------------:
| Client          | {{ $order->client->name }}
@if(!$isClient)
| Created by       | {{ $order->createdBy->name }}
| Client Of        | {{ ucwords($order->brand->name) }}
@endif
| Order Amount     | ${{ $order->price }}
{{-- | Tipped Amount    | ${{ $order->tip_amount }} --}}
| Total Paid       | ${{ $order->price }}
@endcomponent

{{-- @if(!$isClient)
@component('mail::button', ['url' => route('links.show', [$order->id]) ])
View in Dashboard
@endcomponent
@endif --}}

Thanks,<br>
{{ ucwords($order->brand->name) }}
@endcomponent
