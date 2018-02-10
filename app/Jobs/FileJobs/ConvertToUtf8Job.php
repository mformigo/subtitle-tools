<?php

namespace App\Jobs\FileJobs;

use App\Support\Facades\FileName;
use App\Models\StoredFile;
use App\Subtitles\PlainText\PlainText;

class ConvertToUtf8Job extends FileJob
{
    protected $newExtension = '';

    public function handle()
    {
        $this->startFileJob();

        if (! is_text_file($this->inputStoredFile)) {
            return $this->abortFileJob('messages.not_a_text_file');
        }

        $this->newExtension = FileName::getExtension($this->fileJob->original_name, false);

        $textFile = (new PlainText)->loadFile($this->inputStoredFile->filePath);

        $outputStoredFile = StoredFile::createFromTextFile($textFile);

        return $this->finishFileJob($outputStoredFile);
    }

    public function getNewExtension()
    {
        return $this->newExtension;
    }
}
