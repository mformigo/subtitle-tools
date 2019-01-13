<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileNotEmptyRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (! $value instanceof UploadedFile) {
            return false;
        }

        if (! file_exists($value->getRealPath())) {
            return false;
        }

        return filesize($value->getRealPath()) > 0;
    }

    public function message()
    {
        return __('validation.file_is_empty');
    }
}
