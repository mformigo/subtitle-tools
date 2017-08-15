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
            <input type="hidden"   name="stripCurly" value="" />
            <input type="checkbox" name="stripCurly" value="checked" {{ old('stripCurly', 'checked') }}>
        </label>
        <br/>

        <label>
            Strip angle brackets
            <input type="hidden"   name="stripAngle" value="" />
            <input type="checkbox" name="stripAngle" value="checked" {{ old('stripAngle', 'checked') }}>
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