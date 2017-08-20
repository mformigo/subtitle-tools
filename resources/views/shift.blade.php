@extends('base-template')

@section('content')

    <h1>Shift</h1>

    <form method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <label>
            Subtitle file
            <input type="file" name="subtitles[]" multiple>
        </label>

        <label for="ms-input" class="for-number">Shift (in milliseconds):</label>
        <input id="ms-input" placeholder="1000" type="number" name="milliseconds" value="{{ old('milliseconds') }}" required>

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