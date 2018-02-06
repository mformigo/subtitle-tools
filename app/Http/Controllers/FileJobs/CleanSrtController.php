<?php

namespace App\Http\Controllers\FileJobs;

use App\Jobs\FileJobs\CleanSrtJob;
use Illuminate\Http\Request;

class CleanSrtController extends FileJobController
{
    protected $indexRouteName = 'cleanSrt';

    protected $job = CleanSrtJob::class;

    public function index()
    {
        return view('tools.srt-cleaner');
    }

    protected function options(Request $request)
    {
        return [
            'stripCurly'       => $request->get('stripCurly') !== null,
            'stripAngle'       => $request->get('stripAngle') !== null,
            'stripParentheses' => $request->get('stripParentheses') !== null,
        ];
    }
}
