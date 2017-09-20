<?php

namespace App\Jobs;

use App\Facades\FileName;
use App\Models\StoredFile;
use App\Subtitles\PlainText\PlainText;

class ConvertToUtf8Job extends FileJobJob
{
    protected $newExtension = '';

    public function handle()
    {
        $this->startFileJob();

        if(!app(\SjorsO\TextFile\Contracts\TextFileIdentifierInterface::class)->isTextFile($this->inputStoredFile->filePath)) {
            return $this->abortFileJob('messages.not_a_text_file');
        }

        $this->newExtension = FileName::getExtension($this->fileJob->original_name, false);

        $textFile = (new PlainText())->loadFile($this->inputStoredFile->filePath);

        $outputStoredFile = StoredFile::createFromTextFile($textFile);

        return $this->finishFileJob($outputStoredFile);
    }

    public function getNewExtension()
    {
        return $this->newExtension;
    }
}
