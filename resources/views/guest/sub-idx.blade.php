@extends('guest.layout.base-template')

@section('title',       __('seo.title.subIdx'))
@section('description', __('seo.description.subIdx'))
@section('keywords',    __('seo.keywords.subIdx'))

@section('content')

    @component('guest.components.page-intro')

        @slot('title') Convert Sub/Idx to Srt @endslot

        Online tool for changing idx/sub subtitles to srt.
        Output is encoded in UTF-8.
        After uploading your file, you will be redirected to a download page.
        It might take a few minutes before the files are processed, the download page will update automatically.
        <br/>
        <br/>
        To convert from other formats, use the <a href="{{ route('convertToSrt') }}">convert to srt tool</a>

    @endcomponent



    @component('guest.components.tool-form', ['bare' => true])

        @slot('title') Select sub/idx to convert to srt @endslot

        @slot('extraAfter')

            <strong>Select the sub file</strong><br/>
            <div class="file-field input-field mw420">
                <div class="btn">
                    <span>File</span>
                    <input type="file" name="sub" required>
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Select the sub file...">
                </div>
            </div>


            <strong>Select the idx file</strong><br/>
            <p>
                The sub file needs the accompanying idx file to be converted to srt
            </p>
            <div class="file-field input-field mw420">
                <div class="btn">
                    <span>File</span>
                    <input type="file" name="idx" required>
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text" placeholder="Select the idx file...">
                </div>
            </div>

        @endslot

        @slot('buttonText') Convert to Srt @endslot

    @endcomponent


    @component('guest.components.text-section')

        @component('guest.components.text-section-content', ['h2' => true])
            @slot('title') Converting sub/idx to srt online @endslot

            Sub/Idx or VobSub format subtitles are usually extracted from DVDs.
            These subtitles always come in a pair, a sub file that contains picture of the subtitle text, and an idx file that contains information of when to show each picture of text.
            You need both the sub file and the idx file to use them, the subtitles are unusable if you only have one or the other.
            This online tool uses OCR to read the text in each picture, and then matches each line of text with the correct timestamp.
            Converting idx/sub to srt online can take a few minutes, possibly longer if the site is busy.
            The page will update automatically when the file is done processing.
            Because it takes a long time to convert a VobSub file, they can only be uploaded one by one, and not inside an archive file.
        @endcomponent

        @component('guest.components.text-section-content')
            @slot('title') Sub format @endslot

            Sub/idx type subtitles use pictures of text to display subtitles on the screen.
            This works great if you want your subtitles to work on every tv in the world, regardless of what font or text encoding the tv uses.
            Using pictures to display the text also has downsides: editing the text is very hard, the files are relatively big, and the display quality can be low (especially on big screens).
            Nowadays the VobSub format isn't used that much anymore.
            A lot of newer devices such as smartphones and some smart TVs don't support sub/idx subtitles at all.
            This converter tool can be used to convert VobSub subtitles to srt so you can use them on any device.
        @endcomponent

        @component('guest.components.text-section-content')
            @slot('title') Multiple languages @endslot

            Sub/Idx files can contain multiple languages.
            This tool will extract every language that is inside the file.
            It is not possible to only select the specific language you want, so you'll have to wait until all the languages that come before the one you want are done processing.
        @endcomponent

    @endcomponent

@endsection
