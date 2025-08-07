@component('mail::message', [
    'app_name' => ucwords($invoice->brand->name),
    'img_path' => "images/brands" . $invoice->brand->logo_url,
    'img_alt' => ucfirst($invoice->brand->name) . " Logo"
])
New Order with Order # {{ $invoice->id }} has been created

@component('mail::table')
| Column           | Value
| -------------    | -------------:
| Customer         | {{ $invoice->client->name }}
| Created by       | {{ $invoice->createdBy->name }}
| Client Of        | {{ ucwords($invoice->brand->name) }}
| Invoice Amount     | ${{ $invoice->price }}
| Remaining Amount | ${{ $invoice->remaining_price }}
| Total Amount     | ${{ $invoice->price }}
@endcomponent

{{-- @component('mail::button', ['url' => route('links.show', [$order->id]) ])
View in Dashboard
@endcomponent --}}

Thanks,<br>
Koderspedia
@endcomponent
