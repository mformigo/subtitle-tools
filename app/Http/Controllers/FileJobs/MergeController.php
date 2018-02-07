<?php

namespace App\Http\Controllers\FileJobs;

use App\Http\Rules\FileNotEmptyRule;
use App\Http\Rules\SubtitleFileRule;
use App\Jobs\FileJobs\MergeSubtitlesJob;
use App\Models\StoredFile;
use Illuminate\Http\Request;

class MergeController extends FileJobController
{
    protected $indexRouteName = 'merge';

    protected $job = MergeSubtitlesJob::class;

    protected $shouldAlwaysQueue = true;

    protected $extractArchives = false;

    public function index()
    {
        return view('tools.merge-subtitles');
    }

    protected function rules(): array
    {
        return [
            'subtitles'       => ['required', 'file', new FileNotEmptyRule, new SubtitleFileRule],
            'second-subtitle' => ['required', 'file', new FileNotEmptyRule, new SubtitleFileRule],
        ];
    }

    protected function options(Request $request)
    {
        $file = $request->file('second-subtitle');

        return [
            'mergeWithStoredFileId' => StoredFile::getOrCreate($file)->id,
        ];
    }
}
