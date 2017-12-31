@extends('layout.base-template')

@section('title',       __('seo.title.pinyin'))
@section('description', __('seo.description.pinyin'))
@section('keywords',    __('seo.keywords.pinyin'))

@include('helpers.disconnect-echo')

@section('content')

    <h1>Make Pinyin Subtitles</h1>
    <p>
        This tool makes pinyin subtitles by converting Chinese to pinyin.
    </p>


    @component('components.tool-form')

        @slot('title') Select a file to convert to pinyin @endslot

        @slot('formats') Supported subtitle formats: srt, ass, ssa, smi, sub, webvtt @endslot

        @slot('buttonText') Make pinyin subtitles @endslot

        @slot('extraAfter')
            <div class="max-w-xs leading-normal">
                <label class="block cursor-pointer">
                    <input type="radio" name="mode" value="2" {{ old('mode', '2') === '2' ? 'checked' : '' }}>
                    {{ __('tools.pinyin.mode.2') }}
                </label>

                <label class="block cursor-pointer my-3">
                    <input type="radio" name="mode" value="3" {{ old('mode') === '3' ? 'checked' : '' }}>
                    {{ __('tools.pinyin.mode.3') }}
                </label>

                <label class="block cursor-pointer">
                    <input type="radio" name="mode" value="1" {{ old('mode') === '1' ? 'checked' : '' }}>
                    {{ __('tools.pinyin.mode.1') }}
                </label>
            </div>
        @endslot

    @endcomponent


    <h2>About pinyin subtitles</h2>
    <p>
        Watching Chinese movies or tv shows with pinyin subtitles is a great way to practice your Chinese.
        Pinyin (拼音) is the official romanization system for Mandarin Chinese in China and Taiwan.
        In other words, pinyin is a system of phonetic transcriptions of Mandarin Chinese that helps you pronounce the characters.
        <br/><br/>
        This tool changes normal srt or ass subtitles to pinyin subtitles.
        Both simplified and traditional Chinese are supported.
        Subtitles are always converted to srt first.
    </p>

    <h2>VLC not displaying Chinese srt subtitles correctly</h2>
    <p>
        If VLC media player shows Chinese subtitles as weird symbols or as squares, then you either your subtitles are not in unicode UTF-8, or you are using the wrong font in VLC.
        All tools on this website encode files in UTF-8, so if you use this website you can be sure encoding is not the problem.
        Here is <a class="font-bold" href="{{ route('blog.vlcSubtitleBoxes') }}">a simple guide for getting Chinese subtitles to work in VLC.</a>
    </p>

    <h2>Convert modes</h2>
    <p>
        This tool offers three modes for converting Chinese subtitles to pinyin subtitles, they are described below.
    </p>

    <h3>Add pinyin underneath Chinese</h3>
    <p>
        This mode adds a line of pinyin underneath each line that contains a Chinese character.
        Lines that do not have any Chinese in them are left untouched.
    </p>
    <div class="mt-4">
        <span class="toggler py-1 px-3 rounded border cursor-pointer mr-2 bg-grey-light" data-show-id="one-before" data-hide-id="one-after">Before</span>
        <span class="toggler py-1 px-3 rounded border cursor-pointer"                    data-show-id="one-after"  data-hide-id="one-before">After</span>

        <samp id="one-before" class="block mt-4">
            766<br/>
            00:36:32,480 --> 00:36:34,450<br/>
            我已经换了8个老板了<br/>
            <br/><br/>
            767<br/>
            00:36:34,480 --> 00:36:35,610<br/>
            -你说什么？<br/>
            -8个老板.<br/><br/><br/>
        </samp>

        <samp id="one-after" class="block mt-4 hidden">
            766<br/>
            00:36:32,480 --> 00:36:34,450<br/>
            我已经换了8个老板了<br/>
            wǒ yǐjīng huàn le 8 gè lǎobǎn le<br/>
            <br/>
            767<br/>
            00:36:34,480 --> 00:36:35,610<br/>
            -你说什么？<br/>
            - nǐ shuōshímǒ?<br/>
            -8个老板.<br/>
            -8 gè lǎobǎn.
        </samp>
    </div>


    <h3>Add pinyin underneath Chinese and remove non-Chinese lines</h3>
    <p>
        This mode does the same as the mode described above, except it also removes lines that do not contain any Chinese characters.
        Using this mode works best when you have a bi-lingual subtitle file, like one with Chinese on the top line and english on the bottom line.
    </p>
    <div class="mt-4">
        <span class="toggler py-1 px-3 rounded border cursor-pointer mr-2 bg-grey-light" data-show-id="two-before" data-hide-id="two-after">Before</span>
        <span class="toggler py-1 px-3 rounded border cursor-pointer"                    data-show-id="two-after"  data-hide-id="two-before">After</span>

        <samp id="two-before" class="block mt-4">
            987<br/>
            00:59:22,517 --> 00:59:24,142<br/>
            我打乒乓球<br/>
            I played Ping-Pong<br/>
            <br/>
            988<br/>
            00:59:24,218 --> 00:59:27,812<br/>
            甚至没人陪着也能打<br/>
            even when I didn't have anyone to play Ping-Pong with.
        </samp>

        <samp id="two-after" class="block mt-4 hidden">
            987<br/>
            00:59:22,517 --> 00:59:24,142<br/>
            我打乒乓球<br/>
            wǒ dǎ pīngpāngqiú<br/>
            <br/>
            988<br/>
            00:59:24,218 --> 00:59:27,812<br/>
            甚至没人陪着也能打<br/>
            shènzhì méi rén péi zhe yě néng dǎ
        </samp>
    </div>


    <h3>Replace all Chinese with pinyin</h3>
    <p>
        Like the name says, this mode replaces all Chinese characters with pinyin.
        This mode only changes Chinese characters, and leaves all other text unchanged.
    </p>
    <div class="mt-4">
        <span class="toggler py-1 px-3 rounded border cursor-pointer mr-2 bg-grey-light" data-show-id="three-before" data-hide-id="three-after">Before</span>
        <span class="toggler py-1 px-3 rounded border cursor-pointer"                    data-show-id="three-after"  data-hide-id="three-before">After</span>

        <samp id="three-before" class="block mt-4">
            141<br/>
            00:06:42,937 --> 00:06:45,337<br/>
            全是脂肪，那样很不健康<br/>
            <br/>
            142<br/>
            00:06:45,506 --> 00:06:48,270<br/>
            鲸是地球上最大的哺乳动物<br/>
            但是就像乔治说的…<br/>
        </samp>

        <samp id="three-after" class="block mt-4 hidden">
            141<br/>
            00:06:42,937 --> 00:06:45,337<br/>
            quán shì zhīfáng, nàyàng hěn bù jiànkāng<br/>
            <br/>
            142<br/>
            00:06:45,506 --> 00:06:48,270<br/>
            jīng shì dìqiú shàng zuì dà de bǔrǔdòngwù<br/>
            dànshì jiù xiàng qiáozhì shuō de…<br/>
        </samp>
    </div>

@endsection


@push('footer')
    <script>
        $(".toggler").on("click", function() {
            $('#'+this.dataset.showId).removeClass('hidden');
            $('#'+this.dataset.hideId).addClass('hidden');

            $(this).parent().find('.toggler').removeClass('bg-grey-light');
            $(this).addClass('bg-grey-light');
        });
    </script>
@endpush