<?php

namespace App\Jobs\FileJobs;

use App\Subtitles\PlainText\Srt;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Models\StoredFile;
use App\Support\Facades\TextFileFormat;

class MergeSubtitlesJob extends FileJob
{
    public function handle()
    {
        $this->startFileJob();

        $firstSrt = $this->getInputSrt();

        $secondSrt = $this->getMergeWithSrt();

        if (! $firstSrt || ! $secondSrt) {
            return $this->abortFileJob('messages.cant_merge_these_subtitles');
        }

        foreach ($secondSrt->getCues() as $srtCue) {
            $firstSrt->addCue($srtCue);
        }

        $outputStoredFile = StoredFile::createFromTextFile($firstSrt);

        return $this->finishFileJob($outputStoredFile);
    }

    protected function getInputSrt()
    {
        $inputSubtitle = TextFileFormat::getMatchingFormat($this->inputStoredFile);

        return $this->convertToSrt($inputSubtitle);
    }

    protected function getMergeWithSrt()
    {
        $storedFileId = $this->fileGroup->job_options->mergeWithStoredFileId;

        $mergeWithStoredFile = StoredFile::findOrFail($storedFileId);

        $mergeWithSubtitle = TextFileFormat::getMatchingFormat($mergeWithStoredFile);

        return $this->convertToSrt($mergeWithSubtitle);
    }

    protected function convertToSrt($subtitle)
    {
        if (! $subtitle instanceof TransformsToGenericSubtitle && ! $subtitle instanceof Srt) {
            return null;
        }

        $srt = $subtitle instanceof Srt
            ? $subtitle
            : new Srt($subtitle);

        $srt->stripCurlyBracketsFromCues()
            ->stripAngleBracketsFromCues()
            ->removeDuplicateCues();

        if (! $srt->hasCues()) {
            return null;
        }

        return $srt;
    }

    public function getNewExtension()
    {
        return 'srt';
    }
}
