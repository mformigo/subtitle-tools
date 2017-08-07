<?php

namespace App\Jobs;

use App\Models\SubIdxLanguage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExtractSubIdxLanguage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 300;

    private $subIdxLanguage;

    public function __construct(SubIdxLanguage $subIdxLanguage)
    {
        $this->subIdxLanguage = $subIdxLanguage;
    }

    public function handle()
    {
        $VobSub2Srt = $this->subIdxLanguage->subIdx->getVobSub2Srt();

        // See the readme for more information about vobsub2srt behavior
        $outputFilePath = $VobSub2Srt->extractLanguage($this->subIdxLanguage->index);

        $newName = null;

        if(file_exists($outputFilePath)) {
            if(filesize($outputFilePath) === 0) {
                unlink($outputFilePath);
            }
            else {
                // todo: parse it as srt and save it again

                $newName = substr($outputFilePath, 0, strlen($outputFilePath) - 4) . '-' . $this->subIdxLanguage->index . '-' . $this->subIdxLanguage->language . '.srt';

                rename($outputFilePath, $newName);
            }

        }

        if($newName !== null) {
            $this->subIdxLanguage->update([
                'filename' => $this->subIdxLanguage->subIdx->filename . '-' . $this->subIdxLanguage->index . '-' . $this->subIdxLanguage->language . '.srt',
                'error' => false,
                'finished_at' => \Carbon\Carbon::now(),
            ]);
        }
        else {
            $this->subIdxLanguage->update([
                'error' => true,
                'finished_at' => \Carbon\Carbon::now(),
            ]);
        }

    }

    public function failed()
    {
        $this->subIdxLanguage->update([
            'error' => true,
            'finished_at' => \Carbon\Carbon::now(),
        ]);
    }

}
