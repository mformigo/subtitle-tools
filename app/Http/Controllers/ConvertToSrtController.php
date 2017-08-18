<?php

namespace App\Http\Controllers;

use App\Jobs\ConvertToSrtJob;
use Illuminate\Http\Request;

class ConvertToSrtController extends FileJobController
{
    public function index()
    {
        return view('convert-to-srt');
    }

    public function post(Request $request)
    {
        $this->validateFileJob();

        return $this->doFileJobs(ConvertToSrtJob::class);
    }

    protected function getIndexRouteName()
    {
        return 'convert-to-srt';
    }
}
