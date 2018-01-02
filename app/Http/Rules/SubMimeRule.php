<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SubMimeRule implements Rule
{
    public function passes($attribute, $value)
    {
        if ($value instanceof UploadedFile) {
            return file_mime($value->getRealPath()) === 'video/mpeg';
        }

        return false;
    }

    public function message()
    {
        return __('validation.subidx_invalid_sub_mime');
    }
}
