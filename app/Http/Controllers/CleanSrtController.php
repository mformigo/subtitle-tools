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
            'stripCurly' => $request->get('stripCurly') !== null,
            'stripAngle' => $request->get('stripAngle') !== null,
        ];

        return $this->doFileJobs(CleanSrtJob::class, $jobOptions);
    }

    protected function getIndexRouteName()
    {
        return 'clean-srt';
    }
}
