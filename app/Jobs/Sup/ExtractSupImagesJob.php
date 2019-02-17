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
    public $timeout = 300;

    public $queue = 'A200';

    private $supJob;

    public function __construct(SupJob $supJob)
    {
        $this->supJob = $supJob;
    }

    public function handle()
    {
        $debugid = 'D'.$this->supJob->id;

        info($debugid.' - Starting sup job->handle()');

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

        info($debugid.' - Opening sup file');

        try {
            $sup = SupFile::open($this->supJob->inputStoredFile->file_path);
        } catch (Exception $exception) {
            return $this->failed($exception, 'messages.sup.exception_when_reading');
        }

        if (! $sup) {
            return $this->failed(null, 'messages.sup.not_a_sup_file');
        }

        info($debugid.' - done opening, getting indexes');

        $imageFilePaths = [];

        try {
            $cueIndexes = $sup->cueIndexes();

            info($debugid.' - Done getting indexes, starting to extract');

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

        info($debugid.' - Done extracting, preparing to dispatch ocr jobs');

        $this->supJob->extract_time = now()->diffInSeconds($extractingStartedAt);

        $this->supJob->save();

        SupJobProgressChanged::dispatch($this->supJob, 'Finished extracting '.count($imageFilePaths).' images');

        $ocrLanguage = $this->getOcrLanguage();

        $chunks = array_chunk($imageFilePaths, 25);
        $i = 0;

        info($debugid.' - Dispatching '.count($chunks).' chunks of 25 files');

        foreach ($chunks as $filePathsChunk) {

            if ($i % 10 === 0) {
                info($debugid.' - dispatching, at '.$i.' / '.count($chunks));
            }

            OcrImageJob::dispatch(
                $this->supJob->id,
                $filePathsChunk,
                $ocrLanguage
            )->onQueue('A300');

            $i++;
        }

        info($debugid.' - Done!');
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
