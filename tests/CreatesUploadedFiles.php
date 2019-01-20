<?php

namespace Tests;

use Illuminate\Http\UploadedFile;

trait CreatesUploadedFiles
{
    public function createUploadedFile($filePath, $fileName = null)
    {
        if (! starts_with($filePath, $this->testFilesStoragePath)) {
            $filePath = str_finish($this->testFilesStoragePath, DIRECTORY_SEPARATOR).ltrim($filePath, DIRECTORY_SEPARATOR);
        }

        return new UploadedFile(
            $filePath,
            $fileName ?? base_path($filePath),
            null, null, null, true
        );
    }
}
