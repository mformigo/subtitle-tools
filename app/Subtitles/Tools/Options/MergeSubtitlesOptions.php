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

    public function rules(): array
    {
        return [
            'mode'            => 'required|in:simple,nearestCueThreshold',
            'second-subtitle' => ['required', 'file', new FileNotEmptyRule, new SubtitleFileRule],
        ];
    }

    public function loadRequest(Request $request)
    {
        $file = $request->file('second-subtitle');

        return $this->load([
            'mode'                  => $request->get('mode'),
            'mergeWithStoredFileId' => StoredFile::getOrCreate($file)->id,
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
