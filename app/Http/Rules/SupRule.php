<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use SjorsO\Sup\SupFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SupRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (! $value instanceof UploadedFile) {
            return false;
        }

        $filePath = $value->getRealPath();

        $supType = SupFile::getFormat($filePath);

        return $supType !== false;
    }

    public function message()
    {
        return __('validation.not_a_valid_sup_file');
    }
}
