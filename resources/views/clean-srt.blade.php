@extends('base-template')

@section('content')

    <h1>Clean srt</h1>

    <form method="post" enctype="multipart/form-data">
        {{ csrf_field() }}


        <label>
            Subtitle file
            <input type="file" name="subtitles[]" multiple>
        </label>

        <br/>
        <label>
            Strip curly brackets
            <input type="hidden"   name="jo_strip_curly" value="" />
            <input type="checkbox" name="jo_strip_curly" value="checked" {{ old('jo_strip_curly', 'checked') }}>
        </label>
        <br/>

        <label>
            Strip angle brackets
            <input type="hidden"   name="jo_strip_angle" value="" />
            <input type="checkbox" name="jo_strip_angle" value="checked" {{ old('jo_strip_angle', 'checked') }}>
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