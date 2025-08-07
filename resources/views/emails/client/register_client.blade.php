@component('mail::message', [
    'app_name' => ucwords($client->name),
    'img_path' => asset("images/default.png"),
    'img_alt' => ucfirst($client->name) . " Logo"
])
Thanks for registering with us! Use Below Details to login in to your account.

@component('mail::table')
| Column           | Value
| -------------    | -------------:
| Name         | {{ $client->name }}
| email         | {{ $client->email }}
| username         | {{ $client->username }}
| password         | {{ $client->plainPassword }}
@endcomponent

{{-- @component('mail::button', ['url' => route('links.show', [$order->id]) ])
View in Dashboard
@endcomponent --}}

Thanks,<br>
Koderspedia
@endcomponent
