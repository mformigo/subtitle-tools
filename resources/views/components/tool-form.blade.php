<section class="tool">
    <div class="container">
        <form method="post" enctype="multipart/form-data">

            {{ csrf_field() }}

            <div class="card card-tool">
                <div class="card-content">
                    <h2 class="card-title">{{ $title }}</h2>

                    @if(isset($extraBefore))
                        {{ $extraBefore }}
                    @endif

                    @if(!isset($bare))
                        <div class="file-field input-field mw420">
                            <div class="btn">
                                <span>Files</span>
                                <input type="file" name="subtitles[]" multiple required>
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
                        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
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
            </div>

        </form>
    </div>
</section>