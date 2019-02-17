<?php

namespace App\Jobs\Sup;

use App\Events\SupJobChanged;
use App\Events\SupJobProgressChanged;
use App\Jobs\BaseJob;
use App\Models\SupJob;
use App\Models\SupStats;
use App\Support\Tesseract;
use App\Support\Utils\FileName;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use RuntimeException;

class OcrImageJob extends BaseJob implements ShouldQueue
{
    /**
     * Manually stop the job after this many seconds
     */
    public $manualTimeout = 50;

    public $timeout = 60;

    private $supJobId;

    private $imageFilePaths;

    private $ocrLanguage;

    public function __construct($supJobId, $imageFilePaths, $ocrLanguage)
    {
        $this->supJobId = $supJobId;

        $this->imageFilePaths = array_wrap($imageFilePaths);

        $this->ocrLanguage = $ocrLanguage;
    }

    public function handle()
    {
        if ($this->isMarkedAsFailed()) {
            return;
        }

        if ($this->isMarkedAsSlow() && count($this->imageFilePaths) > 1) {
            return $this->dispatchAsSlowJob();
        }

        $jobStartedAt = now();

        foreach ($this->imageFilePaths as $filePath) {
            if (now()->diffInSeconds($jobStartedAt) > $this->manualTimeout) {
                $this->markAsSlow();

                return $this->dispatchAsSlowJob();
            }

            $this->ocrImage($filePath);
        }

        list($index, $total) = $this->parseFileName();

        SupJobProgressChanged::dispatch($this->supJobId, "Reading image $index / $total");

        $this->fireBuildJobIfAllComplete();
    }

    private function ocrImage($filePath)
    {
        if (! file_exists($filePath)) {
            throw new RuntimeException('File does not exist: '.$filePath);
        }

        $ocrStartedAt = microtime(true) * 1000;

        $text = (new Tesseract($filePath))
            ->executable('/usr/bin/tesseract')
            ->quietMode()
            ->suppressErrors()
            ->lang($this->ocrLanguage)
            ->run();

        SupStats::recordImageOcrd(
            (int) round((microtime(true) * 1000) - $ocrStartedAt)
        );

        $text = $this->sanitizeText($text);

        $nameChanger = new FileName();

        $filePath = $nameChanger->appendName($filePath, '--ocr');

        $filePath = $nameChanger->changeExtension($filePath, 'txt');

        file_put_contents($filePath, $text);
    }

    private function getDirectory()
    {
        return str_finish(dirname($this->imageFilePaths[0]), DIRECTORY_SEPARATOR);
    }

    private function sanitizeText($text)
    {
        // sanitize tesseract output, this is a quick hack
        return str_replace([
            '(', ')',
            '[', ']',
            '〈', '〉',
            '〝', '〞',
            '“', '”',
        ], '', $text);
    }

    private function parseFileName()
    {
        $lastFilePath = $this->imageFilePaths[count($this->imageFilePaths) - 1];

        // Files have this name: frame-[00001-00032].png
        preg_match('/\[(\d+)-(\d+)\]/', $lastFilePath, $match);

        return [
            (int)$match[1],
            (int)$match[2],
        ];
    }

    /**
     * If all images for this sup have been OCR'd, we need to fire a job to build the srt file.
     */
    private function fireBuildJobIfAllComplete()
    {
        $directory = $this->getDirectory();

        $allFileNames = scandir($directory);

        $imageCount = collect($allFileNames)->filter(function ($name) {
            return ends_with($name, '.png');
        })->count();

        $textFilesCount = collect($allFileNames)->filter(function ($name) {
            return ends_with($name, '.txt');
        })->count();

        if ($imageCount === $textFilesCount && ! file_exists($directory.'BUILDING')) {
            touch($directory.'BUILDING');

            $supJob = SupJob::findOrFail($this->supJobId);

            BuildSupSrtJob::dispatch($supJob)->onQueue('A100');
        }
    }

    public function failed($e, $errorMessage = null)
    {
        $this->markAsFailed();

        $supJob = SupJob::findOrFail($this->supJobId);

        $supJob->error_message = ($errorMessage === null) ? 'messages.sup.job_failed' : 'messages.sup.job_timed_out';

        $supJob->internal_error_message = $errorMessage ?: (($e instanceof Exception) ? $e->getMessage() : $e);

        $supJob->measureEnd();

        $supJob->save();

        SupJobChanged::dispatch($supJob);
    }

    private function isMarkedAsFailed()
    {
        return file_exists($this->getDirectory().'FAILED');
    }

    private function markAsFailed()
    {
        $madeFile = touch($this->getDirectory().'FAILED');

        if ($madeFile === false) {
            info('OcrImageJob: failed to create a FAILED file in '.$this->getDirectory());
        }
    }

    private function isMarkedAsSlow()
    {
        return file_exists($this->getDirectory().'SLOW');
    }

    private function markAsSlow()
    {
        $madeFile = touch($this->getDirectory().'SLOW');

        if ($madeFile === false) {
            info('OcrImageJob: failed to create a SLOW file in '.$this->getDirectory());
        }
    }

    private function dispatchAsSlowJob()
    {
        foreach ($this->imageFilePaths as $filePath) {
            OcrImageJob::dispatch(
                $this->supJobId,
                $filePath,
                $this->ocrLanguage
            )->onQueue('A400');
        }
    }
}
