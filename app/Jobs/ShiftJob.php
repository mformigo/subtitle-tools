<?php

namespace App\Jobs;

use App\Facades\TextFileFormat;
use App\Models\StoredFile;
use App\Subtitles\ShiftsCues;
use App\Subtitles\TextFile;

class ShiftJob extends FileJobJob
{
    protected $newExtension = '';

    public function handle()
    {
        $this->startFileJob();

        if(!app('TextFileIdentifier')->isTextFile($this->inputStoredFile->filePath)) {
            return $this->abortFileJob('messages.not_a_text_file');
        }

        /** @var $subtitle TextFile */
        $subtitle = TextFileFormat::getMatchingFormat($this->inputStoredFile->filePath);

        if(!$subtitle instanceof ShiftsCues) {
            return $this->abortFileJob('messages.file_can_not_be_shifted');
        }

        $this->newExtension = $subtitle->getExtension();

        $subtitle->shift($this->fileGroup->job_options->milliseconds);

        $outputStoredFile = StoredFile::createFromTextFile($subtitle);

        return $this->finishFileJob($outputStoredFile);
    }

    public function getNewExtension()
    {
        return $this->newExtension;
    }
}
