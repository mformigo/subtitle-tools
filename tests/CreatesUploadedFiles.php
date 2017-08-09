<?php

namespace Tests;

use Illuminate\Http\UploadedFile;

trait CreatesUploadedFiles
{
    private function createUploadedFile($filePath, $fileName)
    {
        return new UploadedFile(
            $filePath,
            $fileName,
            null, null, null, true
        );
    }
}
