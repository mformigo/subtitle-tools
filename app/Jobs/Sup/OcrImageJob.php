<?php

namespace App\Jobs\Sup;

use App\Events\SupJobProgressChanged;
use App\Utils\Support\FileName;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use TesseractOCR;

class OcrImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 12;

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
        if(! file_exists($this->imageFilePath)) {
            throw new Exception('File does not exist: '.$this->imageFilePath);
        }

        $text = (new TesseractOCR($this->imageFilePath))
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
}
