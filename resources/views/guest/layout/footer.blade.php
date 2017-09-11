@if(!isset($hideFooterAd) || $hideFooterAd !== true)
    <div class="container">
        <div class="above-footer-ad">
            @if(App::environment('production'))
                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                {{--ST-above-footer--}}
                <ins class="adsbygoogle"
                     style="display:inline-block;width:728px;height:90px"
                     data-ad-client="ca-pub-8027891891391991"
                     data-ad-slot="2279107549"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            @else
                <ins class="adsbygoogle" style="display:inline-block;width:728px;height:90px;border: 1px solid black"></ins>
            @endif
        </div>
    </div>
@endif

<footer id="Footer" class="right-align">
    <div class="container">
        <a href="{{ route('contact') }}">{{ __('nav.item.contact') }}</a> me for suggestions, questions or bug reports
    </div>
</footer>
