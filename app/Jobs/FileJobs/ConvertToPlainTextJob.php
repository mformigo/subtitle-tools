<?php

namespace App\Jobs\FileJobs;

use App\Subtitles\Tools\ToPlainText;
use App\Support\Facades\TextFileFormat;
use App\Models\StoredFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Support\TextFile\Facades\TextFileIdentifier;

class ConvertToPlainTextJob extends FileJob
{
    public function handle()
    {
        $this->startFileJob();

        if (! TextFileIdentifier::isTextFile($this->inputStoredFile->filePath)) {
            return $this->abortFileJob('messages.not_a_text_file');
        }

        $inputSubtitle = TextFileFormat::getMatchingFormat($this->inputStoredFile->filePath);

        if (! $inputSubtitle instanceof TransformsToGenericSubtitle) {
            return $this->abortFileJob('messages.cant_convert_file_to_plain_text');
        }

        $tool = new ToPlainText();

        $plainText = $tool->convert($inputSubtitle);

        if ($tool->hasError()) {
            return $this->abortFileJob($tool->error);
        }

        $outputStoredFile = StoredFile::createFromTextFile($plainText);

        return $this->finishFileJob($outputStoredFile);
    }

    public function getNewExtension()
    {
        return 'txt';
    }
}
