<?php

namespace App\Http\Controllers\FileJobs;

use App\Jobs\FileJobs\CleanSrtJob;
use App\Subtitles\Tools\Options\SrtCleanerOptions;

class CleanSrtController extends FileJobController
{
    protected $indexRouteName = 'cleanSrt';

    protected $job = CleanSrtJob::class;

    protected $options = SrtCleanerOptions::class;

    public function index()
    {
        return view('tools.srt-cleaner');
    }
}
