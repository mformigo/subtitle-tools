@extends('guest.layout.base-template')

@include('helpers.robots-no-index')

@section('title',       __('seo.title.subIdxDetail'))
@section('description', __('seo.description.subIdxDetail'))
@section('keywords',    __('seo.keywords.subIdxDetail'))

@section('content')

    @component('guest.components.page-intro')
        @slot('title') Sub/Idx Download @endslot

        The srt files are being extracted from the sub/idx file.
        This page will update automatically.
        Extracting a language can take a few minutes, please be patient.
    @endcomponent

    <div class="container">

        <p>
            Extracting srt files from <strong>{{ $originalName }}</strong>
        </p>

        <div class="result-and-ad">

            <sub-idx-languages page-id="{{ $pageId }}"></sub-idx-languages>


            <div class="sub-idx-detail-ad">
                @if(App::environment('production'))
                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                    {{--ST-sub-idx-detail--}}
                    <ins class="adsbygoogle"
                         style="display:inline-block;width:300px;height:250px"
                         data-ad-client="ca-pub-8027891891391991"
                         data-ad-slot="7553762758"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                @else
                    <ins class="adsbygoogle" style="display:inline-block;width:300px;height:250px;border: 1px solid black"></ins>
                @endif
            </div>

        </div>



        <br/>
        <br/>
        <a class="btn" href="{{ route('subIdx') }}">Back to tool</a>

    </div>

@endsection
