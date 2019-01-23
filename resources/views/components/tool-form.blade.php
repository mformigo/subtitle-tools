@php
    $singleFile = $singleFile ?? false;

    $accept = $accept ?? false
@endphp

<h2>{{ $title }}</h2>

<div class="flex md:flex-row flex-col mt-6">

    <form id="drop-container" method="post" enctype="multipart/form-data" class="relative">
        {{ csrf_field() }}

        <div class="hidden dropzone-instructions items-center justify-center flex-col">
            @include('helpers.svg.file', ['classes' => 'w-12'])

            <span class="text-xl mt-4 font-bold">
                {{ $singleFile ? 'Drop a file here' : 'Drop files here' }}
            </span>
        </div>

        <div id="dropzone-error" class="hidden items-center justify-center flex-col">
            @include('helpers.svg.error-circle', ['classes' => 'w-12'])

            <span id="dropzone-error-text" class="text-xl mt-4 w-48 text-center font-bold">
                Oops
            </span>
        </div>

        @isset($extraBefore)
            {{ $extraBefore }}
        @endisset

        @if(! isset($bare))
            <input id="subtitles-input" type="file" {!! $accept ? 'accept="'.$accept.'" ' : '' !!}{!! $singleFile ? 'name="subtitle"' : 'name="subtitles[]" multiple' !!} required>

            @isset($formats)
                <small class="block my-2">{{ $formats }}</small>
            @endisset
        @endif

        @isset($extraAfter)
            @if(! isset($bare))
                <div class="mt-6"></div>
            @endif

            {{ $extraAfter }}
        @endisset

        <button type="submit" class="tool-btn ml-auto">{{ $buttonText }}</button>


        @if ($errors->any())
            <div class="inline-block mt-8 p-3 bg-red-lighter">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

    </form>

    <div class="mx-auto mt-8 md:mt-0">
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
