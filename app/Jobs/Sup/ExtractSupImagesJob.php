<?php

namespace App\Jobs\Sup;

use App\Events\SupJobProgressChanged;
use App\Jobs\BaseJob;
use App\Models\SupStats;
use App\Support\Facades\TempDir;
use App\Models\SupJob;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use SjorsO\Sup\SupFile;

class ExtractSupImagesJob extends BaseJob implements ShouldQueue
{
    public $timeout = 330;

    public $queue = 'A200';

    private $supJob;

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

        SupStats::recordNewSupFile(
            SupFile::getFormat($this->supJob->inputStoredFile->file_path),
            filesize($this->supJob->inputStoredFile->file_path)
        );

        $sup = null;

        try {
            $sup = SupFile::open($this->supJob->inputStoredFile->file_path);
        } catch (Exception $exception) {
            return $this->failed($exception, 'messages.sup.exception_when_reading');
        }

        if (! $sup) {
            return $this->failed(null, 'messages.sup.not_a_sup_file');
        }

        $imageFilePaths = [];

        try {
            $cueIndexes = $sup->cueIndexes();

            foreach ($cueIndexes as $index) {
                $imageFilePaths[] = $sup->extractImage($index, $this->supJob->temp_dir);

                if ($extractingStartedAt->diffInSeconds(now()) > 300) {
                    info('ExtractSupImagesJob: extracting took too long, stopped at '.$index.' / '.count($cueIndexes));

                    return $this->failed(null, 'messages.sup.extracting_images_took_too_long');
                }
            }
        } catch(Exception $exception) {
            return $this->failed($exception, 'messages.sup.exception_when_extracting_images');
        }

        $this->supJob->extract_time = now()->diffInSeconds($extractingStartedAt);

        $this->supJob->save();

        SupJobProgressChanged::dispatch($this->supJob, 'Finished extracting '.count($imageFilePaths).' images');

        $ocrLanguage = $this->getOcrLanguage();

        $dispatchingStartedAt = now();

        $chunks = array_chunk($imageFilePaths, 10);
        $i = 0;

        foreach ($chunks as $filePathsChunk) {
            OcrImageJob::dispatch(
                $this->supJob->id,
                $filePathsChunk,
                $ocrLanguage
            )->onQueue('A300');

            $diff = $dispatchingStartedAt->diffInSeconds(now());
            static $l30 = false;
            static $l60 = false;
            static $l90 = false;
            static $l120 = false;

            if ($diff > 30 && ! $l30) {
                info('Dispatching OcrImage jobs took 30 seconds: '.$i.' / '.count($chunks));
                $l30 = true;
            }
            if ($diff > 60 && ! $l60) {
                info('Dispatching OcrImage jobs took 60 seconds: '.$i.' / '.count($chunks));
                $l60 = true;
            }
            if ($diff > 90 && ! $l90) {
                info('Dispatching OcrImage jobs took 90 seconds: '.$i.' / '.count($chunks));
                $l90 = true;
            }
            if ($diff > 120 && ! $l120) {
                info('Dispatching OcrImage jobs took 120 seconds: '.$i.' / '.count($chunks));
                $l120 = true;
            }

            $i++;
        }
    }

    public function failed($e, $errorMessage = null)
    {
        $this->supJob->update([
            'finished_at' => now(),
            'error_message' => $errorMessage ?: 'messages.sup.job_failed',
            'internal_error_message' => ($e instanceof Exception) ? $e->getMessage() : $e,
        ]);
    }

    private function getOcrLanguage()
    {
        $ocrLanguage = $this->supJob->ocr_language;

        if ($ocrLanguage === 'chinese') {
            $ocrLanguage = 'chi_sim+chi_tra';
        }

        return $ocrLanguage;
    }
}
