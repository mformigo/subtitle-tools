<?php

namespace App\Jobs\FileJobs;

use App\Subtitles\Tools\Options\SrtCleanerOptions;
use App\Support\Facades\TextFileFormat;
use App\Models\StoredFile;
use App\Subtitles\PlainText\Srt;

class CleanSrtJob extends FileJob
{
    /**
     * @var SrtCleanerOptions
     */
    protected $options;

    public function handle()
    {
        $this->startFileJob();

        $this->options = new SrtCleanerOptions($this->fileGroup->job_options);

        if (! is_text_file($this->inputStoredFile->filePath)) {
            return $this->abortFileJob('messages.not_a_text_file');
        }

        $srt = TextFileFormat::getMatchingFormat($this->inputStoredFile->filePath);

        if (! $srt instanceof Srt) {
            return $this->abortFileJob('messages.file_is_not_srt');
        }

        if ($this->options->stripParentheses) {
            $srt->stripParenthesesFromCues();
        }

        if ($this->options->stripCurly) {
            $srt->stripCurlyBracketsFromCues();
        }

        if ($this->options->stripAngle) {
            $srt->stripAngleBracketsFromCues();
        }

        if ($this->options->stripSquare) {
            $srt->stripSquareBracketsFromCues();
        }

        $srt->removeDuplicateCues();

        if (! $srt->hasCues()) {
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
