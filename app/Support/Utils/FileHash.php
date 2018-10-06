<?php

namespace App\Support\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileHash
{
    protected $fileHashCache = [];

    public function make($file)
    {
        $filePath = ($file instanceof UploadedFile) ? $file->getRealPath() : $file;

        if (!file_exists($filePath)) {
            throw new \RuntimeException("File does not exist ({$filePath})");
        }

        if ($this->isCached($filePath)) {
            return $this->getHashFromCache($filePath);
        }

        $hash = sha1_file($filePath);

        $this->fileHashCache[$filePath] = $hash;

        return $hash;
    }

    protected function isCached($filePath)
    {
        return isset($this->fileHashCache[$filePath]);
    }

    protected function getHashFromCache($filePath)
    {
        return $this->fileHashCache[$filePath];
    }
}
