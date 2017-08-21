@extends('base-template')

@section('content')

    <h1>Pinyin Subtitles</h1>

    <form method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <label>
            Subtitle file
            <input type="file" name="subtitles[]" multiple>
        </label>

        <br/>
        <label>
            {{ __('tools.pinyin.mode.1') }}
            <input type="radio" name="mode" value="1" {{ old('mode', '1') === '1' ? 'checked' : '' }}>
        </label>
        <br/>
        <label>
            {{ __('tools.pinyin.mode.2') }}
            <input type="radio" name="mode" value="2" {{ old('mode') === '2' ? 'checked' : '' }}>
        </label>
        <br/>
        <label>
            {{ __('tools.pinyin.mode.3') }}
            <input type="radio" name="mode" value="3" {{ old('mode') === '3' ? 'checked' : '' }}>
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