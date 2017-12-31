<?php

namespace App\Http\Controllers\FileJobs;

use App\Jobs\FileJobs\ConvertToSrtJob;
use Illuminate\Http\Request;

class ConvertToSrtController extends FileJobController
{
    public function index()
    {
        return view('tools.convert-to-srt');
    }

    public function post(Request $request)
    {
        $this->validateFileJob();

        return $this->doFileJobs(ConvertToSrtJob::class);
    }

    protected function getIndexRouteName()
    {
        return 'convertToSrt';
    }
}
