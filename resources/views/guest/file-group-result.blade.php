@extends('guest.layout.base-template')

@include('helpers.robots-no-index')

@section('title',       __('seo.title.fileGroupResult'))
@section('description', __('seo.description.fileGroupResult'))
@section('keywords',    __('seo.keywords.fileGroupResult'))

@section('content')

    @component('guest.components.page-intro')

        @slot('title') Download @endslot

        Your files are being processed.
        Once they are done the page will update automatically.

    @endcomponent

    <div class="container">

        @if($fileCount > 5)
            <file-group-archive url-key="{{ $urlKey }}"></file-group-archive>


            <div class="above-file-job-result-ad">
                @if(App::environment('production'))
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    {{--ST-file-job-download-top--}}
                    <ins class="adsbygoogle"
                         style="display:inline-block;width:728px;height:90px"
                         data-ad-client="ca-pub-8027891891391991"
                         data-ad-slot="9398339224"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                @else
                    <ins class="adsbygoogle" style="display:inline-block;width:728px;height:90px;border: 1px solid black"></ins>
                @endif
            </div>


        @endif

        <file-group-jobs url-key="{{ $urlKey }}"></file-group-jobs>


        <br/>
        <br/>

        <a class="btn" href="{{ $returnUrl }}">Back to tool</a>

    </div>

@endsection
