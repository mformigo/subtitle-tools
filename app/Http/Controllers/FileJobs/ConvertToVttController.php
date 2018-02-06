<?php

namespace App\Http\Controllers\FileJobs;

use App\Jobs\FileJobs\ConvertToVttJob;

class ConvertToVttController extends FileJobController
{
    protected $indexRouteName = 'convertToVtt';

    protected $job = ConvertToVttJob::class;

    public function index()
    {
        return view('tools.convert-to-vtt');
    }
}
