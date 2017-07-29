@extends('base-template')

@section('content')

    <h1>Sub Idx</h1>

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

@endsection