<?php

namespace App\Exceptions;

use App\Models\StoredFile;

class TextEncodingException extends \Exception
{
    protected $filePath;

    protected $detectedEncoding;

    protected $storedFileId;

    public function __construct($filePath, $detectedEncoding)
    {
        $this->filePath = $filePath;

        $this->detectedEncoding = $detectedEncoding;

        $this->storedFileId = StoredFile::getOrCreate($filePath)->id;

        parent::__construct("Unable to detect file encoding of {$filePath}. Detected: '{$detectedEncoding}', but this was not on the whitelist");
    }

    public function report()
    {
        $message = [
            'datetime: ' . \Carbon\Carbon::now(),
            'filepath: ' . $this->filePath,
            'detected encoding: ' . $this->detectedEncoding,
            'stored file id: ' . $this->storedFileId,
        ];

        file_put_contents(
            storage_path('/logs/text-encoding.log'),
            implode('|', $message) . "\r\n",
            FILE_APPEND
        );
    }

    public function render($request)
    {
        return response()->view('errors.text-encoding-exception', [], 500);
    }
}
