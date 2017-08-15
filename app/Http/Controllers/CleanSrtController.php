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

        $jobOptions = [
            'stripCurly' => $request->has('jo_strip_curly'),
            'stripAngle' => $request->has('jo_strip_angle'),
        ];

        return $this->doFileJobs(CleanSrtJob::class, $jobOptions);
    }

    protected function getIndexRouteName()
    {
        return 'clean-srt';
    }
}
