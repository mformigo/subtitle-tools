<section class="tool">
    <div class="container">
        <form id="ToolForm" method="post" enctype="multipart/form-data">

            {{ csrf_field() }}

            <div class="card card-tool">
                <div class="card-content form-content">
                    <h2 class="card-title">{{ $title }}</h2>

                    @if(isset($extraBefore))
                        {{ $extraBefore }}
                    @endif

                    @if(!isset($bare))
                        <div class="file-field input-field mw420">
                            <div class="btn">
                                <span>Files</span>
                                <input type="file" name="subtitles[]" id="SubtitlesInput" multiple required>
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text" placeholder="{{ isset($filePlaceholder) ? $filePlaceholder : 'Select files...' }}">
                            </div>

                            @if(isset($formats))
                                <small>{{ $formats }}</small>
                            @endif
                        </div>
                    @endif

                    @if(isset($extraAfter))
                        {{ $extraAfter }}
                    @endif

                    <div class="form-group right-align">
                        <button type="submit" class="btn">{{ $buttonText }}</button>
                    </div>


                    @if ($errors->any())
                        <div class="alert alert-danger" id="Errors">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                </div>
                <div class="card-content spinner-content hidden">
                    <spinner size="big"></spinner>
                    <p>
                        {{ __('messages.files_are_being_uploaded') }}
                    </p>
                </div>
            </div>

        </form>

        <div class="tool-form-ad">
            @if(App::environment('production'))
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                {{--ST-next-to-tool-form--}}
                <ins class="adsbygoogle"
                     style="display:inline-block;width:300px;height:250px"
                     data-ad-client="ca-pub-8027891891391991"
                     data-ad-slot="2796437708"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            @else
                <ins style="display:inline-block;width:300px;height:250px;border:1px solid black;"></ins>
            @endif
        </div>

    </div>
</section>

@push('inline-footer-scripts')
    <script>
        $("#ToolForm").bind("submit", function() {
            $("div.card-content.form-content").addClass("hidden");

            $("div.card-content.spinner-content").removeClass("hidden");
        });
    </script>
@endpush
