@if(isset($h2))
    <h2>{{ $title }}</h2>
@else
    <h3>{{ $title }}</h3>
@endif

<p>
    {{ $slot }}
</p>

@if(isset($extraAfter))
    {{ $extraAfter }}
@endif
