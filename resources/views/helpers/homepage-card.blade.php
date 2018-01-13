<a class="h-card" href="{{ $route }}">
    <div class="h-card-header">
        <h4 class="h-card-title">{{ $title }}</h4>

        @include('helpers.svg.'.$icon)
    </div>
    <p>
        {{ $text }}
    </p>
</a>
