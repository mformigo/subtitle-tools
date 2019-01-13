<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AreUploadedFilesRule implements Rule
{
    public function passes($attribute, $value)
    {
        $files = array_wrap($value);

        if (! $files) {
            return false;
        }

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                return false;
            }

            if (! $file->isValid()) {
                return false;
            }

            if (! file_exists($file->getRealPath())) {
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return __('validation.uploaded_files');
    }
}
