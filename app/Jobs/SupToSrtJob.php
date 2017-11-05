<?php

namespace App\Jobs;

use App\Events\SupJobChanged;
use App\Facades\TempDir;
use App\Models\StoredFile;
use App\Models\SupJob;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\PlainText\SrtCue;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use SjorsO\Sup\SupFile;

class SupToSrtJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 400;

    protected $supJob;

    public function __construct(SupJob $supJob)
    {
        $this->supJob = $supJob;
    }

    public function handle()
    {
        $jobStartedAt = Carbon::now();

        $this->supJob->measureStart();

        $this->supJob->temp_dir = TempDir::make('sup');

        $sup = null;

        try {
            $sup = SupFile::open($this->supJob->inputStoredFile->file_path);
        }
        catch(Exception $exception) {
            return $this->abortWithError('messages.sup.exception_when_reading', $exception->getMessage());
        }

        if($sup === false) {
            return $this->abortWithError('messages.sup.not_a_sup_file');
        }

        $outputFilePaths = $sup->extractImages($this->supJob->temp_dir);

        $extractingImagesFinishedAt = Carbon::now();

        $cueManifest = $sup->getCueManifest();

        $srt = new Srt();

        $ocrLanguage = $this->supJob->ocr_language;

        if($ocrLanguage === 'chinese'){
            $ocrLanguage = 'chi_sim+chi_tra';
        }

        $imageCount = count($outputFilePaths);

        for($i = 0; $i < $imageCount; $i++) {

            if(Carbon::now()->diffInSeconds($jobStartedAt) > ($this->timeout - 30)) {
                $extractingImagesTime = $extractingImagesFinishedAt->diffInSeconds($jobStartedAt);

                return $this->abortWithError('messages.sup.job_timed_out', "extracting images took {$extractingImagesTime} seconds. Stopped at frame {$i}/{$imageCount}");
            }

            $text = (new \TesseractOCR($outputFilePaths[$i]))
                ->quietMode()
                ->suppressErrors()
                ->lang($ocrLanguage)
                ->run();

            // sanitize tesseract output, this is a quick hack
            $text = str_replace([
                '(', ')',
                '[', ']',
                '〈', '〉',
                '〝', '〞',
                '“', '”',
            ], '', $text);

            $cue = (new SrtCue())
                ->setLines(explode("\n", $text))
                ->setTiming($cueManifest[$i]['startTime'], $cueManifest[$i]['endTime']);

            $srt->addCue($cue);
        }

        $srt->removeEmptyCues();

        if(! $srt->hasCues()) {
            return $this->abortWithError('messages.sup.no_cues_with_dialogue');
        }

        $outputFile = StoredFile::createFromTextFile($srt);

        $this->supJob->output_stored_file_id = $outputFile->id;

        $this->supJob->measureEnd();

        $this->supJob->save();

        return $this->endJob();
    }

    public function failed()
    {
        return $this->abortWithError('messages.sup.job_failed');
    }

    protected function abortWithError($errorMessage, $internalErrorMessage = null)
    {
        $this->supJob->error_message = $errorMessage;

        $this->supJob->internal_error_message = $internalErrorMessage;

        $this->supJob->measureEnd();

        $this->supJob->save();

        return $this->endJob();
    }

    protected function endJob()
    {
        event(
            new SupJobChanged($this->supJob)
        );

        if(is_dir($this->supJob->temp_dir)) {
            $globPattern = str_finish($this->supJob->temp_dir, '/').'*';

            foreach(glob($globPattern) as $filePath) {
                unlink($filePath);
            }

            rmdir($this->supJob->temp_dir);
        }

        return $this->supJob;
    }
}
