<?php

namespace App\Http\Rules;

use App\Subtitles\PlainText\PlainText;
use App\Support\Facades\TextFileFormat;
use Illuminate\Contracts\Validation\Rule;
use SjorsO\TextFile\Facades\TextFileIdentifier;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SubtitleFileRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (! $value instanceof UploadedFile) {
            return false;
        }

        $filePath = $value->getRealPath();

        if (! TextFileIdentifier::isTextFile($filePath)) {
            return false;
        }

        $format = TextFileFormat::getMatchingFormat($filePath, false);

        if ($format instanceof PlainText) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return __('validation.file_is_not_a_subtitle_file');
    }
}
