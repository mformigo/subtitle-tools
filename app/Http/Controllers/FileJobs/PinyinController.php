<?php

namespace App\Http\Controllers\FileJobs;

use App\Jobs\FileJobs\PinyinSubtitlesJob;
use Illuminate\Http\Request;

class PinyinController extends FileJobController
{
    public function index()
    {
        return view('tools.pinyin-subtitles');
    }

    public function post(Request $request)
    {
        $this->validateFileJob([
            'mode' => 'required|in:1,2,3'
        ]);

        $jobOptions = [
            // mode name is only set because it makes debugging/diagnostics easier
            'mode_name' => __('tools.pinyin.mode.' . $request->get('mode')),
            'mode' => $request->get('mode'),
        ];

        return $this->doFileJobs(PinyinSubtitlesJob::class, $jobOptions, true);
    }

    protected function getIndexRouteName()
    {
        return 'pinyin';
    }
}
