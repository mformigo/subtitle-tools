@php
    $singleFile = ($singleFile ?? false);
@endphp

<div class="md:hidden block mt-6">
    @include('helpers.ads.tool-form-large-mobile-banner')
</div>

<h2>{{ $title }}</h2>

<div class="flex mt-6">

    <form id="drop-container" method="post" enctype="multipart/form-data" class="relative">
        {{ csrf_field() }}

        <div class="hidden dropzone-instructions items-center justify-center flex-col">
            @include('helpers.svg.file', ['classes' => 'w-12'])

            <span class="text-xl mt-4">
                {{ $singleFile ? 'Drop a file here' : 'Drop files here' }}
            </span>
        </div>

        @isset($extraBefore)
            {{ $extraBefore }}
        @endisset

        @if(! isset($bare))
            <input id="subtitles-input" type="file" {!! $singleFile ? 'name="subtitle"' : 'name="subtitles[]" multiple' !!} required>

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


        var dropContainer = document.getElementById("drop-container");

        var subtitlesInput = document.getElementById("subtitles-input");

        // The sub/idx tool uses different inputs, and should not have drag and drop enabled.
        var defaultDropInputExists = !! subtitlesInput;

        // Dragging and dropping doesn't work in IE11
        var isIE11 = window.MSInputMethodContext && document.documentMode;

        var dragLeaveTimeout = null;

        function isDraggingFiles(dragEvent) {
            types = dragEvent.dataTransfer.types;

            return types && types.indexOf
                        ? types.indexOf('Files') !== -1
                        : types.contains('Files');
        }

        dropContainer.ondragenter = function (event) {
            event.preventDefault();

            if (isDraggingFiles(event) && defaultDropInputExists && ! isIE11) {
                dropContainer.classList.add("dropzone-active");
            }
        };

        dropContainer.ondragleave = function (event) {
            clearTimeout(dragLeaveTimeout);

            dragLeaveTimeout = window.setTimeout(function () {
                dropContainer.classList.remove("dropzone-active");
            }, 50);
        };

        dropContainer.ondragover = function (event) {
            event.preventDefault();

            event.dataTransfer.dropEffect = 'copy';

            clearTimeout(dragLeaveTimeout);
        };

        dropContainer.ondrop = function(event) {
            event.preventDefault();
            event.stopPropagation();

            dropContainer.classList.remove("dropzone-active");
            
            if (! isDraggingFiles(event) || ! defaultDropInputExists && ! isIE11) {
                return;
            }

            if (! subtitlesInput.multiple && event.dataTransfer.files.length > 1) {
                subtitlesInput.value = '';
            } else {
                subtitlesInput.files = event.dataTransfer.files;
            }
        };
    </script>
@endpush
