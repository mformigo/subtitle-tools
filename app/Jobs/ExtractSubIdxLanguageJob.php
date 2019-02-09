<?php

namespace App\Jobs;

use App\Models\StoredFile;
use App\Models\SubIdxLanguage;
use App\Subtitles\PlainText\Srt;
use App\Support\Facades\VobSub2Srt;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExtractSubIdxLanguageJob extends BaseJob implements ShouldQueue
{
    // the shell_exec in VobSub2Srt times out after 300 seconds
    public $timeout = 330;

    public $queue = 'B200';

    public $subIdxLanguage;

    public function __construct(SubIdxLanguage $subIdxLanguage)
    {
        $this->subIdxLanguage = $subIdxLanguage;
    }

    public function handle()
    {
        $this->subIdxLanguage->update(['started_at' => now()]);

        $outputFilePath = VobSub2Srt::get()
            ->path($this->subIdxLanguage->subIdx->file_path_without_extension)
            ->extract($this->subIdxLanguage->index, $this->subIdxLanguage->language);

        if (! file_exists($outputFilePath)) {
            return $this->abort('messages.subidx_no_vobsub2srt_output_file');
        }

        if (filesize($outputFilePath) === 0) {
            unlink($outputFilePath);

            return $this->abort('messages.subidx_empty_vobsub2srt_output_file');
        }

        $srt = new Srt($outputFilePath);

        if (count($srt->getCues()) === 0) {
            unlink($outputFilePath);

            return $this->abort('messages.subidx_vobsub2srt_output_file_only_empty_cues');
        }

        $this->subIdxLanguage->update([
            'output_stored_file_id' => StoredFile::createFromTextFile($srt)->id,
            'finished_at' => now(),
        ]);

        unlink($outputFilePath);

        return $this->subIdxLanguage;
    }

    public function failed()
    {
        return $this->abort('messages.subidx_job_failed');
    }

    private function abort($error)
    {
        $this->subIdxLanguage->update([
            'error_message' => $error,
            'finished_at' => now(),
        ]);

        return $this->subIdxLanguage;
    }
}
