<?php

namespace App\Http\Controllers;

use App\Jobs\CleanSrtJob;
use Illuminate\Http\Request;

class CleanSrtController extends FileJobController
{
    public function index()
    {
        return view('clean-srt');
    }

    public function post(Request $request)
    {
        $this->validateFileJob();

        return $this->doFileJobs(CleanSrtJob::class);
    }

    protected function getIndexRouteName()
    {
        return 'clean-srt';
    }
}
