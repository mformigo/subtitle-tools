<?php

namespace App\Subtitles\Tools\Options;

use App\Http\Rules\FileNotEmptyRule;
use App\Http\Rules\SubtitleFileRule;
use App\Models\StoredFile;
use Illuminate\Http\Request;

class MergeSubtitlesOptions extends ToolOptions
{
    public $mergeWithStoredFileId;

    public $mode = 'simple';

    public $nearestCueThreshold = 1000;

    public function rules(): array
    {
        return [
            'mode'                  => 'required|in:simple,nearestCueThreshold',
            'nearest_cue_threshold' => 'required|numeric|not_in:0|regex:/^\d+$/',
            'second-subtitle'       => ['required', 'file', new FileNotEmptyRule, new SubtitleFileRule],
        ];
    }

    public function loadRequest(Request $request)
    {
        $file = $request->file('second-subtitle');

        return $this->load([
            'mode'                  => $request->get('mode'),
            'mergeWithStoredFileId' => StoredFile::getOrCreate($file)->id,
            'nearestCueThreshold'   => (int) $request->get('nearest_cue_threshold'),
        ]);
    }

    public function getMergeStoredFile(): StoredFile
    {
        return StoredFile::findOrFail($this->mergeWithStoredFileId);
    }

    public function simpleMode()
    {
        return $this->mode === 'simple';
    }

    public function nearestCueThresholdMode()
    {
        return $this->mode === 'nearestCueThreshold';
    }
}
