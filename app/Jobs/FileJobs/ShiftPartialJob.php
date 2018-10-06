<?php

namespace App\Jobs\FileJobs;

use App\Support\Facades\TextFileFormat;
use App\Models\StoredFile;
use App\Subtitles\PartialShiftsCues;
use App\Subtitles\TextFile;
use App\Support\TextFile\Facades\TextFileIdentifier;

class ShiftPartialJob extends FileJob
{
    protected $newExtension = '';

    public function handle()
    {
        $this->startFileJob();

        if (! TextFileIdentifier::isTextFile($this->inputStoredFile->filePath)) {
            return $this->abortFileJob('messages.not_a_text_file');
        }

        /** @var $subtitle TextFile */
        $subtitle = TextFileFormat::getMatchingFormat($this->inputStoredFile->filePath);

        if (!$subtitle instanceof PartialShiftsCues) {
            return $this->abortFileJob('messages.file_can_not_be_partial_shifted');
        }

        $this->newExtension = $subtitle->getExtension();

        $shifts = $this->fileGroup->job_options->shifts;

        foreach ($shifts as $shift) {
            $fromMs = $this->timestampToMilliseconds($shift->from);

            $toMs = $this->timestampToMilliseconds($shift->to);

            $subtitle->shiftPartial($fromMs, $toMs, $shift->milliseconds);
        }

        $outputStoredFile = StoredFile::createFromTextFile($subtitle);

        return $this->finishFileJob($outputStoredFile);
    }

    public function getNewExtension()
    {
        return $this->newExtension;
    }

    private function timestampToMilliseconds($timestamp)
    {
        list($hours, $minutes, $seconds) = explode(':', $timestamp);

        return ($hours * 60 * 60 * 1000) +
               ($minutes    * 60 * 1000) +
               ($seconds         * 1000);
    }
}
