<x-mail::layout>
    {{-- Header --}}
    <x-slot:header>
        @include('vendor.mail.html.header', [
            'img_path' => $img_path,
            'img_alt' => $img_alt
        ])
    </x-slot:header>

    {{-- Body --}}
    {!! $slot !!}
    
        {{-- Subcopy --}}
            @isset($subcopy)
                        <x-slot:subcopy>
                    <x-mail::subcopy>
                    {!! $subcopy !!}
                </x-mail::subcopy>
                    </x-slot:subcopy>
            @endisset
        
            {{-- Footer --}}
        <x-slot:footer>
    <x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
