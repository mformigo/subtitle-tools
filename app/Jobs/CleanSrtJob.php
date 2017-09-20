<?php

namespace App\Jobs;

use App\Facades\TextFileFormat;
use App\Models\StoredFile;
use App\Subtitles\PlainText\Srt;

class CleanSrtJob extends FileJobJob
{
    public function handle()
    {
        $this->startFileJob();

        if(!app(\SjorsO\TextFile\Contracts\TextFileIdentifierInterface::class)->isTextFile($this->inputStoredFile->filePath)) {
            return $this->abortFileJob('messages.not_a_text_file');
        }

        $srt = TextFileFormat::getMatchingFormat($this->inputStoredFile->filePath);

        if(!$srt instanceof Srt) {
            return $this->abortFileJob('messages.file_is_not_srt');
        }

        $jobOptions = $this->fileGroup->job_options;

        if($jobOptions->stripCurly ?? true) {
            $srt->stripCurlyBracketsFromCues();
        }

        if($jobOptions->stripAngle ?? true) {
            $srt->stripAngleBracketsFromCues();
        }

        $srt->removeDuplicateCues();

        if(!$srt->hasCues()) {
            return $this->abortFileJob('messages.file_has_no_dialogue');
        }

        $outputStoredFile = StoredFile::createFromTextFile($srt);

        return $this->finishFileJob($outputStoredFile);
    }

    public function getNewExtension()
    {
        return "srt";
    }
}
