<?php

namespace Tests;

use Illuminate\Http\UploadedFile;

trait CreatesUploadedFiles
{
    private function createUploadedFile($filePath, $fileName = null)
    {
        return new UploadedFile(
            $filePath,
            $fileName ?? base_path($filePath),
            null, null, null, true
        );
    }
}
