@extends('guest.layout.base-template')

@section('title',       __('seo.title.convertToUtf8'))
@section('description', __('seo.description.convertToUtf8'))
@section('keywords',    __('seo.keywords.convertToUtf8'))

@section('content')

    @component('guest.components.page-intro')

        @slot('title') Convert files to UTF-8 @endslot

        This tool converts any text or subtitle file to UTF-8 encoding.

    @endcomponent

    @component('guest.components.tool-form')

        @slot('title') Select files to convert to UTF-8 @endslot

        @slot('formats') Supported formats: any text file @endslot

        @slot('buttonText') Convert to UTF-8 @endslot

    @endcomponent


    <section class="text-content">
        <div class="container">

            <h2>Fixing text encoding</h2>
            <p>
                Text encoding is a tricky thing. Years ago, there were hundreds of different text encodings in an attempt to support all languages and character sets.
                Nowadays all these different languages can be encoded in UTF-8, but unfortunately all the files from years ago are still around, and some stubborn countries still use old text encodings.
                Many devices have trouble displaying text encodings that are not UTF-8, they will display the text are random, unreadable characters.
                <br/><br/>
                This tool converts the uploaded text files to UTF-8 so modern devices can properly read them.
                You can uploaded multiple files at the same time, or upload a zip file.
            </p>

            <h3>Other tools</h3>
            <p>
                All the other tools on this website automatically detect text encoding and return their output in UTF-8.
                When using this website, you don't have to worry about text encoding.
            </p>

        </div>
    </section>

@endsection
