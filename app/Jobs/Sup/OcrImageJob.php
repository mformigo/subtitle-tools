<?php

namespace App\Jobs\Sup;

use App\Events\SupJobChanged;
use App\Events\SupJobProgressChanged;
use App\Jobs\BaseJob;
use App\Models\SupJob;
use App\Utils\Support\FileName;
use Exception;
use TesseractOCR;

class OcrImageJob extends BaseJob
{
    public $timeout = 30;

    protected $supJobId;

    protected $imageFilePath;

    protected $ocrLanguage;

    public function __construct($supJobId, $imageFilePath, $ocrLanguage)
    {
        $this->supJobId = $supJobId;

        $this->imageFilePath = $imageFilePath;

        $this->ocrLanguage = $ocrLanguage;
    }

    public function handle()
    {
        if(file_exists($this->getDirectory().'FAILED')) {
            return;
        }

        if(! file_exists($this->imageFilePath)) {
            throw new Exception('File does not exist: '.$this->imageFilePath);
        }

        $text = (new TesseractOCR($this->imageFilePath))
            ->executable('/usr/bin/tesseract')
            ->quietMode()
            ->suppressErrors()
            ->lang($this->ocrLanguage)
            ->run();

        $text = $this->sanitizeText($text);

        $nameChanger = new FileName();

        $filePath = $nameChanger->appendName($this->imageFilePath, '--ocr');

        $filePath = $nameChanger->changeExtension($filePath, 'txt');

        file_put_contents($filePath, $text);

        list($index, $total) = $this->parseFileName($this->imageFilePath);

        if($index % 5 === 0 || $index === $total) {
            SupJobProgressChanged::dispatch($this->supJobId, "Reading image $index / $total");
        }

        $this->fireBuildJobIfAllComplete();
    }

    protected function getDirectory()
    {
        return $directory = str_finish(dirname($this->imageFilePath), DIRECTORY_SEPARATOR);
    }

    protected function sanitizeText($text)
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

    protected function parseFileName($imageFilePath)
    {
        // Files have this name: frame-[00001-00032].png
        preg_match('/\[(\d+)-(\d+)\]/', $imageFilePath, $match);

        return [
            (int)$match[1],
            (int)$match[2],
        ];
    }

    /**
     * If all images for this sup have been OCR'd, we need to fire a job to build the srt file.
     */
    protected function fireBuildJobIfAllComplete()
    {
        $directory = $this->getDirectory();

        $allFileNames = scandir($directory);

        $imageCount = collect($allFileNames)->filter(function ($name) {
            return ends_with($name, '.png');
        })->count();

        $textFilesCount = collect($allFileNames)->filter(function ($name) {
            return ends_with($name, '.txt');
        })->count();

        if($imageCount === $textFilesCount && ! file_exists($directory.'BUILDING')) {
            touch($directory.'BUILDING');

            $supJob = SupJob::findOrFail($this->supJobId);

            BuildSupSrtJob::dispatch($supJob)->onQueue('larry-high');
        }
    }

    public function failed($e)
    {
        $madeFile = touch($this->getDirectory().'FAILED');

        if($madeFile === false) {
            info('OcrImageJob: failed to create a FAILED file in '.$this->getDirectory());
        }

        $supJob = SupJob::findOrFail($this->supJobId);

        $supJob->error_message = 'messages.sup.job_failed';

        $supJob->internal_error_message = ($e instanceof Exception) ? $e->getMessage() : $e;

        $supJob->measureEnd();

        $supJob->save();

        SupJobChanged::dispatch($supJob);
    }
}
