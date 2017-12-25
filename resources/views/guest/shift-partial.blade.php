@extends('guest.layout.base-template')

@section('title',       __('seo.title.shiftPartial'))
@section('description', __('seo.description.shiftPartial'))
@section('keywords',    __('seo.keywords.shiftPartial'))

@include('helpers.disconnect-echo')

@section('content')

    <h1>Partial Subtitle Resync</h1>
    <p>
        Shift multiple parts of a subtitle file.
        <br/><br/>
        The <a href="{{ route('shift') }}">shifter tool</a> adjusts the whole file, this tool only adjusts specific parts it.
    </p>

    @component('guest.components.tool-form')

        @slot('title') Select a file to resync @endslot

        @slot('formats') Supported subtitle formats: srt, ass, ssa, webvtt @endslot

        @slot('buttonText') Shift @endslot

        @slot('extraAfter')
            <div class="">
                <div class="flex border-b pb-2">
                    <div class="w-24 mr-2 font-bold">From</div>
                    <div class="w-24 mr-2 font-bold">To</div>
                    <div class="w-16 mr-2 font-bold">Shift</div>
                </div>

                @php
                    $shifts = old('shifts', [['from' => '', 'to' => '', 'milliseconds' => '']]);

                    $shifts = array_map(function ($arr) {
                        return (object) $arr;
                    }, $shifts);

                    foreach ($shifts as $shift) {
                        $shift->isValid = preg_match('/\d\d:\d\d:\d\d/', $shift->from) &&
                                          preg_match('/\d\d:\d\d:\d\d/', $shift->to)   &&
                                          preg_match('/-?\d+/', $shift->milliseconds)  &&
                                          str_replace(':', '', $shift->to) > str_replace(':', '', $shift->from);

                        $shift->isEmpty = empty($shift->from) && empty($shift->to) && empty($shift->milliseconds);
                    }
                @endphp

                @foreach ($shifts as $shift)
                    <div class="cloneable flex p-1 my-2 rounded {{ $shift->isValid || $shift->isEmpty ? '' : 'bg-red-lighter' }}">
                        <input name="shifts[{{ $loop->iteration }}][from]"         value="{{ $shift->from }}"         class="field mr-2 w-24" placeholder="hh:mm:ss" title="valid input is HH:MM:SS (23:59:59)" required type="text" pattern="\d\d:\d\d:\d\d"/>
                        <input name="shifts[{{ $loop->iteration }}][to]"           value="{{ $shift->to }}"           class="field mr-2 w-24" placeholder="hh:mm:ss" title="valid input is HH:MM:SS (23:59:59)" required type="text" pattern="\d\d:\d\d:\d\d"/>
                        <input name="shifts[{{ $loop->iteration }}][milliseconds]" value="{{ $shift->milliseconds }}" class="field mr-2 w-24" placeholder="1000"     title="shift in milliseconds"              required type="number">
                        <a onclick="deleteRow(this)" class="deleter w-8 pt-2 cursor-pointer text-center">✖</a>
                    </div>
                @endforeach

                <div class="cloner-row flex flex-row-reverse mb-8">
                    <a class="cloner cursor-pointer w-8 text-center text-2xl font-bold">+</a>
                </div>

            </div>
        @endslot

    @endcomponent


    @push('footer')
        <script>
            {{ 'var formInt = '.count($shifts).';' }}

            $("input[type=text]").mask("99:99:99", {placeholder: "-"});

            $(".cloner").on("click", function() {
                var newRow = $(".cloneable").last().clone();

                newRow.removeClass('bg-red-lighter');

                var timeFields = newRow.find("input[type=text]");

                timeFields.mask("99:99:99", {placeholder: "-"});

                timeFields.first().val(timeFields.last().val());
                timeFields.last().val("");

                newRow.find("input[type=number]").val("");

                formInt++;

                newRow.find("input").each(function (el) {
                    this.name = this.name.replace(/\[\d+\]/, function (str) {
                        return '[' + formInt + ']';
                    });
                });
                
                $(".cloner-row").before(newRow);

                timeFields.last().focus();
            });

            function deleteRow(el) {
                var parent = $(el).closest("div");

                $(".cloneable").length > 1
                    ? parent.remove()
                    : parent.find("input").val("");
            }

            $(".cloneable input").on("focus", function () {
                $(this).closest("div").removeClass("bg-red-lighter");
            });
        </script>
    @endpush

    <h2>How to sync subtitles with this tool</h2>
    <p>
        This tool can be used to permanently sync subtitles with movies if the subtitles are off by a different amount of seconds in multiple parts of the video.
        If the subtitles have the same delay the whole video, use the <a href="{{ route('shift') }}">shifter tool</a> instead.
        <br/><br/>
        This is an example of when this tool will work: if you have a 20 minute long movie with subtitles,
        and in the first 10 minutes of the movie the subtitles have a delay of 2 seconds, and the last 10 minutes have a delay of 4 seconds,
        you can fill in the following values to fix the subtitles:
    </p>
    <ul class="my-3">
        <li class="mt-1">From: 00:00:00, To: 00:10:00, Ms: 2000</li>
        <li class="mt-1">From: 00:10:00, To: 00:20:00, Ms: 4000</li>
    </ul>
    <p>
        Entering these values will apply a 2 second shift for the first 10 minutes, and a 4 second shift the last 10 minutes.
        <br/><br/>
        This tool will only work if the subtitles get more out of sync at specific points.
        It will not work if the subtitles gradually get more ouf of sync.
        <br/><br/>
        The following subtitle formats can be resynced: srt, ssa, ass, webvtt.
        Multiple files can be uploaded at the same time, you can also upload a zip or a rar file.
    </p>

    <h3>Learning more about shifting</h3>
    <p>
        To learn more about syncing subtitles, read the information on the <a href="{{ route('shift') }}">shifter tool</a> page.
    </p>

@endsection
