<div class="md:hidden block mt-6">
    @include('helpers.ads.tool-form-large-mobile-banner')
</div>

<h2>{{ $title }}</h2>

<div class="flex mt-6">

    <form method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        @if(isset($extraBefore))
            {{ $extraBefore }}
        @endif

        @if(! isset($bare))
            <input id="SubtitlesInput" type="file" {!! ($singleFile ?? false) ? 'name="subtitle"' : 'name="subtitles[]" multiple' !!} required>

            @if(isset($formats))
                <small class="block my-2">{{ $formats }}</small>
            @endif
        @endif

        @if(isset($extraAfter))
            @if(! isset($bare))
                <div class="mt-6"></div>
            @endif

            {{ $extraAfter }}
        @endif

        <button type="submit" class="tool-btn ml-auto">{{ $buttonText }}</button>


        @if ($errors->any())
            <div class="inline-block mt-8 p-3 bg-red-lighter">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

    </form>

    <div class="mx-auto md:block hidden">
        @include('helpers.ads.tool-form-large-rectangle')
    </div>

</div>


@push('footer')
    <script>
        $("form").bind("submit", function() {
            $('.tool-btn')
                .addClass('bg-grey hover:bg-grey cursor-not-allowed')
                .html("Uploading...")
                .prop('disabled', true);
        });
    </script>
@endpush
