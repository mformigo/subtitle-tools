<?php

namespace App\Jobs;

use App\Facades\TextFileFormat;
use App\StoredFile;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\TransformsToGenericSubtitle;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ConvertToSrtJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use MakesTextFileJobs;

    public $tries = 1;

    public function __construct(StoredFile $storedFile, string $originalName, $jobOptions = null)
    {
        $this->storedFile = $storedFile;

        $this->originalName = $originalName;

        $this->toolRouteName = 'convert-to-srt-index';

        // TODO: implement custom job options
        $this->jobOptions = [
            'job name' => 'ConvertToSrtJob',
            'actions' => [
                'load srt from generic subtitle',
                'strip curly brackets from cues',
                'strip angle brackets from cues',
                'remove duplicate cues',
            ],
            'output line endings' => "\r\n",
            'output encoding' => 'UTF-8 BOM',
        ];
    }

    public function handle()
    {
        $this->makeTextFileJob();

        if($this->textFileJob->finished_at !== null) {
            $this->textFileJob->save();

            return $this->textFileJob;
        }

        $inputSubtitle = TextFileFormat::getMatchingFormat($this->storedFile->filePath);

        if(!$inputSubtitle instanceof TransformsToGenericSubtitle) {
            return $this->setTextFileJobError("Cant transform to srt");
        }

        $srt = new Srt($inputSubtitle);

        $srt->stripCurlyBracketsFromCues()
            ->stripAngleBracketsFromCues()
            ->removeDuplicateCues();

        if(!$srt->hasCues()) {
            return $this->setTextFileJobError("No valid dialogue to convert");
        }

        $storedOutputFile = StoredFile::createFromTextFile($srt);

        $this->textFileJob->output_stored_file_id = $storedOutputFile->id;

        $this->textFileJob->new_extension = $srt->getExtension();

        $this->textFileJob->finished_at = Carbon::now();

        $this->textFileJob->save();

        return $this->textFileJob;
    }
}
