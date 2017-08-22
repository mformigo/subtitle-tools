@extends('base-template')

@section('title',       __('seo.title.'))
@section('description', __('seo.description.'))
@section('keywords',    __('seo.keywords.'))

@section('content')

    <h1>Convert Sub/Idx to Srt</h1>

    <form method="post" enctype="multipart/form-data">
        {{ csrf_field() }}


        <label>
            Sub file:
            <input type="file" name="sub" required>
        </label>
        <br/>

        <label>
            Idx file:
            <input type="file" name="idx" required>
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