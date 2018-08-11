@extends('layout.base-template')

@section('title',       __('seo.title.stats'))
@section('description', __('seo.description.stats'))
@section('keywords',    __('seo.keywords.stats'))

@include('helpers.dont-connect-echo')

@section('content')

    <h1>Stats</h1>
    <p class="max-w-md">
        I started working on SubtitleTools.com in January 2017.
        Back then the only tool the site had was the convert to srt tool.
        I made it to convert Chinese subtitles so they would work correctly on my TV, that way I could watch movies together with my girlfriend.
        <br><br>
        I've kept working on the site since then, mostly on tools I needed myself, but also on tools and features requested by users.
        As of August 2018, the site attracts more than 2000 unique users per day.
    </p>


    <h2>Yesterday</h2>
    <p class="mb-8">
        These are the tool statistics of {{ now()->subDays(1)->format('l, \t\h\e jS \\of F') }}.
    </p>

    <div class="overflow-x-auto">
        <div class="w-176">
            <div class="flex max-w-md font-bold mb-1 pb-1 border-b">
                <div class="w-1/4">Tool</div>
                <div class="w-1/4">Times used</div>
                <div class="w-1/4">File count</div>
                <div class="w-1/4">Total file size</div>
            </div>
            @foreach($fileJobStatsYesterday as $toolRoute => $toolStats)
                <div class="flex max-w-md p-1 hover:bg-grey-lighter">
                    <div class="w-1/4">{{ $toolRoute }}</div>
                    <div class="w-1/4">{{ number_format($toolStats['times_used']) }}</div>
                    <div class="w-1/4">{{ number_format($toolStats['total_files']) }}</div>
                    <div class="w-1/4">{{ format_file_size($toolStats['total_size']) }}</div>
                </div>
            @endforeach
        </div>
    </div>


    <h2>Last month</h2>
    <p class="mb-8">
        These are the tool statistics of {{ now()->startOfMonth()->subDays(1)->format('F') }}.
    </p>

    <div class="overflow-x-auto">
        <div class="w-176">
            <div class="flex max-w-md font-bold mb-1 pb-1 border-b">
                <div class="w-1/4">Tool</div>
                <div class="w-1/4">Times used</div>
                <div class="w-1/4">File count</div>
                <div class="w-1/4">Total file size</div>
            </div>
            @foreach($fileJobStatsLastMonth as $toolRoute => $toolStats)
                <div class="flex max-w-md p-1 hover:bg-grey-lighter">
                    <div class="w-1/4">{{ $toolRoute }}</div>
                    <div class="w-1/4">{{ number_format($toolStats['times_used']) }}</div>
                    <div class="w-1/4">{{ number_format($toolStats['total_files']) }}</div>
                    <div class="w-1/4">{{ format_file_size($toolStats['total_size']) }}</div>
                </div>
            @endforeach
        </div>
    </div>



    <h2 class="mt-8">All time</h2>
    <p class="mb-8">
        These are the total tool statistics since I started recording them on 2018-02-18.
    </p>

    <div class="overflow-x-auto">
        <div class="w-176">
            <div class="flex max-w-md font-bold mb-1 pb-1 border-b">
                <div class="w-1/4">Tool</div>
                <div class="w-1/4">Times used</div>
                <div class="w-1/4">File count</div>
                <div class="w-1/4">Total file size</div>
            </div>
            @foreach($fileJobStatsAllTime as $toolRoute => $toolStats)
                <div class="flex max-w-md p-1 hover:bg-grey-lighter">
                    <div class="w-1/4">{{ $toolRoute }}</div>
                    <div class="w-1/4">{{ number_format($toolStats['times_used']) }}</div>
                    <div class="w-1/4">{{ number_format($toolStats['total_files']) }}</div>
                    <div class="w-1/4">{{ format_file_size($toolStats['total_size']) }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{--fileJobStatsLastMonth--}}
    {{--fileJobStatsAllTime--}}

@endsection
