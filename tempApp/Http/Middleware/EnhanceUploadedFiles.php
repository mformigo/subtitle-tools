<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class EnhanceUploadedFiles extends TransformsRequestFiles
{
    protected function transform($key, UploadedFile $file)
    {
        // we use the _originalName property when extracting zips,
        // to keep everything simple, we add it here to single files too

        $file->_originalName = $file->getClientOriginalName();

        return $file;
    }
}
