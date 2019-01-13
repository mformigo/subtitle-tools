<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TextFileRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (! $value instanceof UploadedFile) {
            return false;
        }

        return is_text_file($value->getRealPath());
    }

    public function message()
    {
        return __('validation.file_is_not_a_textfile');
    }
}
