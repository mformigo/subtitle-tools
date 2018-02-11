<?php

namespace App\Jobs\FileJobs;

use App\Subtitles\ContainsGenericCues;
use App\Subtitles\PlainText\GenericSubtitleCue;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\TextFile;
use App\Subtitles\Tools\Options\MergeSubtitlesOptions;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Models\StoredFile;
use App\Support\Facades\TextFileFormat;
use RuntimeException;

class MergeSubtitlesJob extends FileJob
{
    protected $fileExtension = '';

    public function handle()
    {
        $this->startFileJob();

        $options = new MergeSubtitlesOptions((array) $this->fileGroup->job_options);

        $baseSubtitle = $this->getBaseSubtitle();

        $this->fileExtension = $baseSubtitle->getExtension();

        $mergeSubtitle = $this->getMergeWithSubtitle($options);

        if (! $baseSubtitle || ! $mergeSubtitle) {
            return $this->abortFileJob('messages.cant_merge_these_subtitles');
        }

        if ($options->simpleMode()) {
            $outputSubtitle = $this->simpleMerge($baseSubtitle, $mergeSubtitle);
        } elseif ($options->nearestCueThresholdMode()) {
            $outputSubtitle = $this->nearestCueThresholdMerge($baseSubtitle, $mergeSubtitle);
        } else {
            throw new RuntimeException('Invalid mode');
        }

        $outputStoredFile = StoredFile::createFromTextFile($outputSubtitle);

        return $this->finishFileJob($outputStoredFile);
    }

    /**
     * @return null|ContainsGenericCues|TextFile
     */
    protected function getBaseSubtitle()
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
    protected function getMergeWithSubtitle(MergeSubtitlesOptions $options)
    {
        $mergeWithStoredFile = $options->getMergeStoredFile();

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

    /**
     * @param $baseSubtitle ContainsGenericCues|TextFile
     * @param $mergeSubtitle ContainsGenericCues|TextFile
     *
     * @return ContainsGenericCues|TextFile
     */
    protected function simpleMerge($baseSubtitle, $mergeSubtitle)
    {
        foreach ($mergeSubtitle->getCues() as $mergeCue) {
            $baseSubtitle->addCue($mergeCue);
        }

        $baseSubtitle->removeEmptyCues()
            ->removeDuplicateCues();

        return $baseSubtitle;
    }

    /**
     * @param $baseSubtitle ContainsGenericCues|TextFile
     * @param $mergeSubtitle ContainsGenericCues|TextFile
     *
     * @return ContainsGenericCues|TextFile
     */
    protected function nearestCueThresholdMerge($baseSubtitle, $mergeSubtitle)
    {
        $baseCues = $baseSubtitle->getCues();

        foreach ($mergeSubtitle->getCues() as $cue) {
            $nearestCue = $this->findNearestCue($baseCues, $cue);

            if ($nearestCue === null) {
                // There is no cue nearby within the threshold,
                // just merge the whole cue.
                $baseSubtitle->addCue($cue);
            } else {
                $nearestCue->addLines($cue->getLines());
            }
        }

        return $baseSubtitle;
    }

    /**
     * @param $baseCues GenericSubtitleCue[]
     * @param $cue GenericSubtitleCue
     *
     * @return GenericSubtitleCue|null
     */
    protected function findNearestCue($baseCues, $cue)
    {
        $cueStartMs = $cue->getStartMs();

        $nearestCue = null;

        $smallestDifference = 99999;

        foreach ($baseCues as $cue) {
            $difference = abs($cue->getStartMs() - $cueStartMs);

            // Cues within 400ms are considered a nearby cue.
            if ($difference > 400) {
                continue;
            }

            if ($difference > $smallestDifference) {
                continue;
            }

            $smallestDifference = $difference;

            $nearestCue = $cue;
        }

        return $nearestCue;
    }

    public function getNewExtension()
    {
        return $this->fileExtension;
    }
}
