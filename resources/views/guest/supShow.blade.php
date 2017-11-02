@extends('guest.layout.base-template')

@include('helpers.robots-no-index')

@section('title',       __('seo.title.'))
@section('description', __('seo.description.'))
@section('keywords',    __('seo.keywords.'))

@section('content')

    @component('guest.components.page-intro')

        @slot('title') Sup to Srt Download @endslot

        Your sup file is being converted.
        Once it is done the page will update automatically.

    @endcomponent

    <section class="file-group-result single-file">
        <div class="container">

            <div class="archive-and-ad">
                <div class="above-file-job-result-ad">
                    @if(App::environment('production'))
                        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <ins class="adsbygoogle"
                             style="display:inline-block;width:300px;height:250px"
                             data-ad-client="ca-pub-8027891891391991"
                             data-ad-slot="3208706526"></ins>
                        <script>
                            (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                    @else
                        <ins class="adsbygoogle" style="display:inline-block;width:300px;height:250px;border: 1px solid black"></ins>
                    @endif
                </div>
            </div>

            <sup-job url-key="{{ $urlKey }}"></sup-job>

        </div>
    </section>

    <div class="container">
        <br/>
        <br/>

        <a class="btn" href="{{ $returnUrl }}">Back to tool</a>
    </div>


@endsection
