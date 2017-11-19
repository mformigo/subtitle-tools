<?php

namespace App\Jobs\Sup;

use App\Events\SupJobProgressChanged;
use App\Support\Facades\TempDir;
use App\Models\SupJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use SjorsO\Sup\SupFile;

class ExtractSupImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 230;

    protected $supJob;

    public function __construct(SupJob $supJob)
    {
        $this->supJob = $supJob;
    }

    public function handle()
    {
        SupJobProgressChanged::dispatch($this->supJob, 'Extracting images');

        $extractingStartedAt = Carbon::now();

        $this->supJob->measureStart();

        $this->supJob->temp_dir = TempDir::make('sup');

        $this->supJob->save();

        $sup = null;

        try {
            $sup = SupFile::open($this->supJob->inputStoredFile->file_path);
        }
        catch(Exception $exception) {
            return $this->failed($exception, 'messages.sup.exception_when_reading');
        }

        if($sup === false) {
            return $this->failed(null, 'messages.sup.not_a_sup_file');
        }

        try {
            $imageFilePaths = $sup->extractImages($this->supJob->temp_dir);
        }
        catch(Exception $exception) {
            return $this->failed($exception, 'messages.sup.exception_when_extracting_images');
        }

        $this->supJob->extract_time = Carbon::now()->diffInSeconds($extractingStartedAt);

        $this->supJob->save();

        SupJobProgressChanged::dispatch($this->supJob, 'Finished extracting '.count($imageFilePaths).' images');

        $ocrLanguage = $this->getOcrLanguage();

        foreach($imageFilePaths as $imageFilePath) {
            OcrImageJob::dispatch(
                $this->supJob->id,
                $imageFilePath,
                $ocrLanguage
            )->onQueue('larry-low');
        }
    }

    public function failed($e, $errorMessage = null)
    {
        $this->supJob->error_message = $errorMessage ?: 'messages.sup.job_failed';

        $this->supJob->internal_error_message = ($e instanceof Exception) ? $e->getMessage() : $e;

        $this->supJob->save();
    }

    protected function getOcrLanguage()
    {
        $ocrLanguage = $this->supJob->ocr_language;

        if($ocrLanguage === 'chinese') {
            $ocrLanguage = 'chi_sim+chi_tra';
        }

        return $ocrLanguage;
    }
}
