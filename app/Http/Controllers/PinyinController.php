<?php

namespace App\Http\Controllers;

use App\Jobs\FileJobs\PinyinSubtitlesJob;
use Illuminate\Http\Request;

class PinyinController extends FileJobController
{
    protected $indexRouteName = 'pinyin';

    protected $job = PinyinSubtitlesJob::class;

    protected $shouldAlwaysQueue = true;

    public function index()
    {
        return view('tools.pinyin-subtitles');
    }

    protected function rules(): array
    {
        return [
            'mode' => 'required|in:1,2,3'
        ];
    }

    protected function options(Request $request)
    {
        return [
            'mode' => $request->get('mode'),
        ];
    }
}
