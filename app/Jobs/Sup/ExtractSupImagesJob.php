<?php

namespace App\Jobs\Sup;

use App\Events\SupJobProgressChanged;
use App\Jobs\BaseJob;
use App\Support\Facades\TempDir;
use App\Models\SupJob;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use SjorsO\Sup\SupFile;

class ExtractSupImagesJob extends BaseJob implements ShouldQueue
{
    public $timeout = 330;

    protected $supJob;

    public function __construct(SupJob $supJob)
    {
        $this->supJob = $supJob;
    }

    public function handle()
    {
        SupJobProgressChanged::dispatch($this->supJob, 'Extracting images');

        $extractingStartedAt = now();

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

        if ($sup === false) {
            return $this->failed(null, 'messages.sup.not_a_sup_file');
        }

        $imageFilePaths = [];

        try {
            $cueIndexes = $sup->cueIndexes();

            foreach ($cueIndexes as $index) {
                $imageFilePaths[] = $sup->extractImage($index, $this->supJob->temp_dir);

                if ($extractingStartedAt->diffInSeconds(now()) > 300) {
                    info('ExtractSupImagesJob: extracting took too long, stopped at '.$index.' / '.count($cueIndexes));

                    return $this->failed(new Exception, 'messages.sup.extracting_images_took_too_long');
                }
            }
        }
        catch(Exception $exception) {
            return $this->failed($exception, 'messages.sup.exception_when_extracting_images');
        }

        $this->supJob->extract_time = now()->diffInSeconds($extractingStartedAt);

        $this->supJob->save();

        SupJobProgressChanged::dispatch($this->supJob, 'Finished extracting '.count($imageFilePaths).' images');

        $ocrLanguage = $this->getOcrLanguage();

        foreach (array_chunk($imageFilePaths, 10) as $filePathsChunk) {
            OcrImageJob::dispatch(
                $this->supJob->id,
                $filePathsChunk,
                $ocrLanguage
            )->onQueue('A300');
        }

        // fix a memory leak this job causes
        Artisan::call('queue:restart');
    }

    public function failed($e, $errorMessage = null)
    {
        $this->supJob->update([
            'finished_at'            => now(),
            'error_message'          => $errorMessage ?: 'messages.sup.job_failed',
            'internal_error_message' => ($e instanceof Exception) ? $e->getMessage() : $e,
        ]);
    }

    protected function getOcrLanguage()
    {
        $ocrLanguage = $this->supJob->ocr_language;

        if ($ocrLanguage === 'chinese') {
            $ocrLanguage = 'chi_sim+chi_tra';
        }

        return $ocrLanguage;
    }
}
