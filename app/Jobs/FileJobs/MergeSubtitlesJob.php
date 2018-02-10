<?php

namespace App\Jobs\FileJobs;

use App\Subtitles\ContainsGenericCues;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Models\StoredFile;
use App\Support\Facades\TextFileFormat;

class MergeSubtitlesJob extends FileJob
{
    protected $fileExtension = '';

    public function handle()
    {
        $this->startFileJob();

        $baseSubtitle = $this->getInputSubtitle();

        $this->fileExtension = $baseSubtitle->getExtension();

        $mergeSubtitle = $this->getMergeWithSubtitle();

        if (! $baseSubtitle || ! $mergeSubtitle) {
            return $this->abortFileJob('messages.cant_merge_these_subtitles');
        }

        foreach ($mergeSubtitle->getCues() as $srtCue) {
            $baseSubtitle->addCue($srtCue);
        }

        $baseSubtitle->removeEmptyCues()
            ->removeDuplicateCues();

        $outputStoredFile = StoredFile::createFromTextFile($baseSubtitle);

        return $this->finishFileJob($outputStoredFile);
    }

    /**
     * @return null|ContainsGenericCues|TextFile
     */
    protected function getInputSubtitle()
    {
        $inputSubtitle = TextFileFormat::getMatchingFormat($this->inputStoredFile);

        if (! $inputSubtitle instanceof ContainsGenericCues) {
            return null;
        }

        return $inputSubtitle;
    }

    /**
     * @return null|ContainsGenericCues|TextFile
     */
    protected function getMergeWithSubtitle()
    {
        $storedFileId = $this->fileGroup->job_options->mergeWithStoredFileId;

        $mergeWithStoredFile = StoredFile::findOrFail($storedFileId);

        $mergeWithSubtitle = TextFileFormat::getMatchingFormat($mergeWithStoredFile);

        if (! $mergeWithSubtitle instanceof ContainsGenericCues) {
            return $this->convertToSrt($mergeWithSubtitle);
        }

        return $mergeWithSubtitle;
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
        return $this->fileExtension;
    }
}
