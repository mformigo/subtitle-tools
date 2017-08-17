<?php

namespace App\Jobs;

use App\Events\ExtractingSubIdxLanguageChanged;
use App\Models\StoredFile;
use App\Models\SubIdxLanguage;
use App\Subtitles\PlainText\Srt;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExtractSubIdxLanguageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    // the shell_exec in VobSub2Srt times out after 300 seconds
    public $timeout = 330;

    protected $subIdxLanguage;

    public function __construct(SubIdxLanguage $subIdxLanguage)
    {
        $this->subIdxLanguage = $subIdxLanguage;
    }

    public function handle()
    {
        $this->subIdxLanguage->update(['started_at' => Carbon::now()]);

        $VobSub2Srt = $this->subIdxLanguage->subIdx->getVobSub2Srt();

        // See the readme for more information about vobsub2srt behavior
        $outputFilePath = $VobSub2Srt->extractLanguage($this->subIdxLanguage->index);

        if(!file_exists($outputFilePath)) {
            return $this->abortWithError('subidx_no_vobsub2srt_output_file');
        }

        if(filesize($outputFilePath) === 0) {
            unlink($outputFilePath);

            return $this->abortWithError('subidx_empty_vobsub2srt_output_file');
        }

        $srt = new Srt($outputFilePath);

        if(count($srt->getCues()) === 0) {
            unlink($outputFilePath);

            return $this->abortWithError('subidx_vobsub2srt_output_file_only_empty_cues');
        }

        $storedFile = StoredFile::createFromTextFile($srt);

        $this->subIdxLanguage->update([
            'output_stored_file_id' => $storedFile->id,
            'finished_at' => Carbon::now(),
        ]);

        return $this->subIdxLanguage;
    }

    public function failed()
    {
        return $this->abortWithError('subidx_job_failed');
    }

    protected function abortWithError($errorMessage)
    {
        $this->subIdxLanguage->update([
            'error_message' => $errorMessage,
            'finished_at' => Carbon::now(),
        ]);

        return $this->subIdxLanguage;
    }
}
