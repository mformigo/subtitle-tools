<?php

namespace App\Http\Controllers\FileJobs;

use App\Http\Rules\FileNotEmptyRule;
use App\Http\Rules\SubtitleFileRule;
use App\Jobs\FileJobs\MergeSubtitlesJob;
use App\Subtitles\ContainsGenericCues;
use App\Subtitles\Tools\Options\MergeSubtitlesOptions;
use App\Support\Facades\TextFileFormat;

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
            'subtitles' => [
                'bail',
                'required',
                'file',
                new FileNotEmptyRule,
                new SubtitleFileRule,
                function ($attribute, $value, $fail) {
                    $inputSubtitle = TextFileFormat::getMatchingFormat($value->getRealPath(), false);

                    return $inputSubtitle instanceof ContainsGenericCues
                        ? null
                        : $fail('The base subtitle format is not supported');
                },
            ],
        ];
    }
}
