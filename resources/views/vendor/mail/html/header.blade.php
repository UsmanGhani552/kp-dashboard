@props([
    'url' => config('app.url'),
    'img_path' => null,
    'img_alt' => null
])

<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (!empty($img_path))
                <img src="{{ $img_path }}" class="logo" alt="{{ $img_alt ?? config('app.name') }}">
            @else
                {{ config('app.name') }}
            @endif
        </a>
    </td>
</tr>
