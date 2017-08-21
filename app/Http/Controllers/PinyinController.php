<?php

namespace App\Http\Controllers;

use App\Jobs\CleanSrtJob;
use App\Jobs\PinyinSubtitlesJob;
use Illuminate\Http\Request;

class PinyinController extends FileJobController
{
    public function index()
    {
        return view('pinyin');
    }

    public function post(Request $request)
    {
        $this->validateFileJob([
            'mode' => 'required|in:1,2,3'
        ]);

        $jobOptions = [
            'mode_name' => __('tools.pinyin.mode.' . $request->get('mode')),
            'mode' => $request->get('mode'),
        ];

        return $this->doFileJobs(PinyinSubtitlesJob::class, $jobOptions);
    }

    protected function getIndexRouteName()
    {
        return 'pinyin';
    }
}
