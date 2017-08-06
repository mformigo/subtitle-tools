@extends('base-template')

@section('content')

    <h1>Convert Sub/Idx to Srt</h1>

    <form method="post" enctype="multipart/form-data">
        {{ csrf_field() }}


        <label>
            Sub file:
            <input type="file" name="sub">
        </label>
        <br/>

        <label>
            Idx file:
            <input type="file" name="idx">
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