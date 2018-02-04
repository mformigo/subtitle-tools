<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use SjorsO\TextFile\Facades\TextFileIdentifier;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TextFileRule implements Rule
{
    public function passes($attribute, $value)
    {
        if ($value instanceof UploadedFile) {
            return TextFileIdentifier::isTextFile($value->getRealPath());
        }

        return false;
    }

    public function message()
    {
        return __('validation.file_is_not_a_textfile');
    }
}
