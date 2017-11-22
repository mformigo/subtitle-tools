<?php

namespace App\Jobs\Sup;

use App\Events\SupJobProgressChanged;
use App\Jobs\BaseJob;
use App\Support\Facades\TempDir;
use App\Models\SupJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Artisan;
use SjorsO\Sup\SupFile;

class ExtractSupImagesJob extends BaseJob
{
    public $timeout = 300;

    public $tries = 2;

    protected $supJob;

    public function __construct(SupJob $supJob)
    {
        $this->supJob = $supJob;
    }

    public function handle()
    {
        if($this->attempts() === 2) {
            info('ExtractSupImagesJob is being attempted twice for some reason');

            return true;
        }

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

        foreach(array_chunk($imageFilePaths, 10) as $filePathsChunk) {
            OcrImageJob::dispatch(
                $this->supJob->id,
                $filePathsChunk,
                $ocrLanguage
            )->onQueue('larry-low');
        }

        // fix a memory leak this job causes
        Artisan::call('queue:restart');
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
