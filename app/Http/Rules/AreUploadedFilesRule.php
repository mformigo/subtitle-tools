<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AreUploadedFilesRule implements Rule
{
    public function passes($attribute, $value)
    {
        $uploadedFiles = request()->files->get($attribute);

        foreach (array_wrap($uploadedFiles) as $file) {
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
