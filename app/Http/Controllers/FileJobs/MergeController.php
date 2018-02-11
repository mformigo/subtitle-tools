<?php

namespace App\Http\Controllers\FileJobs;

use App\Http\Rules\FileNotEmptyRule;
use App\Http\Rules\SubtitleFileRule;
use App\Jobs\FileJobs\MergeSubtitlesJob;
use App\Subtitles\Tools\Options\MergeSubtitlesOptions;

class MergeController extends FileJobController
{
    protected $indexRouteName = 'merge';

    protected $job = MergeSubtitlesJob::class;

    /**
     * @var MergeSubtitlesOptions
     */
    protected $options = MergeSubtitlesOptions::class;

    protected $shouldAlwaysQueue = true;

    protected $extractArchives = false;

    public function index()
    {
        return view('tools.merge-subtitles');
    }

    protected function rules(): array
    {
        return [
            'subtitles' => ['required', 'file', new FileNotEmptyRule, new SubtitleFileRule],
        ];
    }
}
