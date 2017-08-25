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

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getDetectedEncoding()
    {
        return $this->detectedEncoding;
    }

    public function getStoredFileId()
    {
        return $this->storedFileId;
    }
}
