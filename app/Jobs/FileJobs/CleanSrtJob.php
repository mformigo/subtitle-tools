<?php

namespace App\Jobs\FileJobs;

use App\Subtitles\Tools\Options\SrtCleanerOptions;
use App\Subtitles\Tools\SrtCleaner;
use App\Support\Facades\TextFileFormat;
use App\Models\StoredFile;
use App\Subtitles\PlainText\Srt;

class CleanSrtJob extends FileJob
{
    /** @var SrtCleanerOptions $options */
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

        (new SrtCleaner)->clean($srt, $this->options);

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
