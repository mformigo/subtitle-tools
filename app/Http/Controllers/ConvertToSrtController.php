<?php

namespace App\Http\Controllers;

use App\Jobs\FileJobs\ConvertToSrtJob;

class ConvertToSrtController extends FileJobController
{
    protected $indexRouteName = 'convertToSrt';

    protected $job = ConvertToSrtJob::class;

    public function index()
    {
        return view('tools.convert-to-srt');
    }
}
