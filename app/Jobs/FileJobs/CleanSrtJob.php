<?php

namespace App\Jobs\FileJobs;

use App\Support\Facades\TextFileFormat;
use App\Models\StoredFile;
use App\Subtitles\PlainText\Srt;
use SjorsO\TextFile\Facades\TextFileIdentifier;

class CleanSrtJob extends FileJob
{
    public function handle()
    {
        $this->startFileJob();

        if(! TextFileIdentifier::isTextFile($this->inputStoredFile->filePath)) {
            return $this->abortFileJob('messages.not_a_text_file');
        }

        $srt = TextFileFormat::getMatchingFormat($this->inputStoredFile->filePath);

        if(! $srt instanceof Srt) {
            return $this->abortFileJob('messages.file_is_not_srt');
        }

        $jobOptions = $this->fileGroup->job_options;

        if($jobOptions->stripParentheses ?? true) {
            $srt->stripParenthesesFromCues();
        }

        if($jobOptions->stripCurly ?? true) {
            $srt->stripCurlyBracketsFromCues();
        }

        if($jobOptions->stripAngle ?? true) {
            $srt->stripAngleBracketsFromCues();
        }

        $srt->removeDuplicateCues();

        if(! $srt->hasCues()) {
            return $this->abortFileJob('messages.file_has_no_dialogue');
        }

        $outputStoredFile = StoredFile::createFromTextFile($srt);

        return $this->finishFileJob($outputStoredFile);
    }

    public function getNewExtension()
    {
        return 'srt';
    }
}
