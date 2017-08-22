@extends('base-template')

@section('title',       __('seo.title.convert_to_srt'))
@section('description', __('seo.description.convert_to_srt'))
@section('keywords',    __('seo.keywords.convert_to_srt'))

@section('content')

    <h1>Convert Subtitles to Srt</h1>

    <form method="post" enctype="multipart/form-data">
        {{ csrf_field() }}


        <label>
            Subtitle file
            <input type="file" name="subtitles[]" multiple>
        </label>

        <br/>

        <button type="submit">Convert</button>

    </form>

    <br/>
    <br/>

    @if ($errors->any())
        <div class="alert alert-danger" id="Errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

@endsection