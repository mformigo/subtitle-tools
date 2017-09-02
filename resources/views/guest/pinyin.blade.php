@extends('guest.layout.base-template')

@section('title',       __('seo.title.pinyin'))
@section('description', __('seo.description.pinyin'))
@section('keywords',    __('seo.keywords.pinyin'))

@section('content')

    @component('guest.components.page-intro')

        @slot('title') Make Pinyin Subtitles @endslot

        This tool makes pinyin subtitles by converting Chinese to pinyin.
        It is excellent for learners who want to practice Chinese by watching movies and tv shows.
        Both simplified and traditional Chinese are supported.
        Subtitles are always converted to srt first.

    @endcomponent


    @component('guest.components.tool-form')

        @slot('title') Select a file to convert to pinyin @endslot

        @slot('formats') Supported subtitle formats: srt, ass, ssa, smi, zip @endslot

        @slot('buttonText') Make pinyin subtitles @endslot

        @slot('extraAfter')
            <div class="options-group radios">

                <input id="pinyinMode2" type="radio" name="mode" value="2" {{ old('mode', '2') === '2' ? 'checked' : '' }}>
                <label for="pinyinMode2">{{ __('tools.pinyin.mode.2') }}</label>

                <input id="pinyinMode3" type="radio" name="mode" value="3" {{ old('mode') === '3' ? 'checked' : '' }}>
                <label for="pinyinMode3">{{ __('tools.pinyin.mode.3') }}</label>

                <input id="pinyinMode1" type="radio" name="mode" value="1" {{ old('mode') === '1' ? 'checked' : '' }}>
                <label for="pinyinMode1">{{ __('tools.pinyin.mode.1') }}</label>

            </div>
        @endslot

    @endcomponent


    @component('guest.components.text-section')

        @component('guest.components.text-section-content', ['h2' => true])
            @slot('title') About pinyin subtitles @endslot

            Watching Chinese movies or tv shows with pinyin subtitles is a great way to practice your Chinese.
            Pinyin (拼音) is the official romanization system for Mandarin Chinese in China and Taiwan.
            In other words, pinyin is a system of phonetic transcriptions of Mandarin Chinese that helps you pronounce the characters.
            This tool changes normal srt or ass subtitles in to pinyin subtitles.
        @endcomponent

        @component('guest.components.text-section-content', ['h2' => true])
            @slot('title') Convert modes @endslot

            This tool offers three modes for converting Chinese subtitles to pinyin subtitles, they are described below.
        @endcomponent

        @component('guest.components.text-section-content')
            @slot('title') Add pinyin underneath Chinese @endslot

            This mode adds a line of pinyin underneath each line that contains a Chinese character.
            Lines that do not have any Chinese in them are left untouched.

            @slot('extraAfter')
                <ul class="tabs pinyin-tabs">
                    <li class="tab col s3"><a href="#one-before">Before</a></li>
                    <li class="tab col s3"><a href="#one-after" class="active">After</a></li>
                </ul>

                <div id="one-before" class="col s12">
                    <samp class="subtitle-example">
                        766<br/>
                        00:36:32,480 --> 00:36:34,450<br/>
                        我已经换了8个老板了<br/>
                        <br/>
                        767<br/>
                        00:36:34,480 --> 00:36:35,610<br/>
                        -你说什么？<br/>
                        -8个老板.<br/><br/>
                    </samp>
                </div>

                <div id="one-after" class="col s12">
                    <samp class="subtitle-example">
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
                        -8 gè lǎobǎn.<br/><br/>
                    </samp>
                </div>
            @endslot

        @endcomponent

        @component('guest.components.text-section-content')
            @slot('title') Add pinyin underneath Chinese and remove non-Chinese lines @endslot

            This mode does the same as the mode described above, except it also removes lines that do not contain any Chinese characters.
            Using this mode works best when you have a bi-lingual subtitle file, like one with Chinese on the top line and english on the bottom line.

            @slot('extraAfter')
                <ul class="tabs pinyin-tabs">
                    <li class="tab col s3"><a href="#two-before">Before</a></li>
                    <li class="tab col s3"><a href="#two-after" class="active">After</a></li>
                </ul>

                <div id="two-before" class="col s12">
                    <samp class="subtitle-example">
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
                </div>

                <div id="two-after" class="col s12">
                    <samp class="subtitle-example">
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
            @endslot

        @endcomponent

        @component('guest.components.text-section-content')
            @slot('title') Replace all Chinese with pinyin @endslot

            Like the name says, this mode replaces all Chinese characters with pinyin.
            This mode only changes Chinese characters, and leaves all other text unchanged.

            @slot('extraAfter')
                <ul class="tabs pinyin-tabs">
                    <li class="tab col s3"><a href="#three-before">Before</a></li>
                    <li class="tab col s3"><a href="#three-after" class="active">After</a></li>
                </ul>

                <div id="three-before" class="col s12">
                    <samp class="subtitle-example">
                        141<br/>
                        00:06:42,937 --> 00:06:45,337<br/>
                        全是脂肪，那样很不健康<br/>
                        <br/>
                        142<br/>
                        00:06:45,506 --> 00:06:48,270<br/>
                        鲸是地球上最大的哺乳动物<br/>
                        但是就像乔治说的…<br/>
                    </samp>
                </div>

                <div id="three-after" class="col s12">
                    <samp class="subtitle-example">
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
            @endslot

        @endcomponent

    @endcomponent


@endsection
