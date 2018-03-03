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

    /**
     * @var MergeSubtitlesOptions
     */
    protected $options;

    public function handle()
    {
        $this->startFileJob();

        $this->options = new MergeSubtitlesOptions($this->fileGroup->job_options);

        $baseSubtitle = $this->getBaseSubtitle();

        $this->fileExtension = $baseSubtitle->getExtension();

        $mergeSubtitle = $this->getMergeWithSubtitle();

        if (! $baseSubtitle || ! $mergeSubtitle) {
            return $this->abortFileJob('messages.cant_merge_these_subtitles');
        }

        if ($this->options->simpleMode() || $this->options->topBottomMode()) {
            $outputSubtitle = $this->simpleMerge($baseSubtitle, $mergeSubtitle);
        } elseif ($this->options->nearestCueThresholdMode()) {
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
    protected function getMergeWithSubtitle()
    {
        $mergeWithStoredFile = $this->options->getMergeStoredFile();

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
            $addedCue = $baseSubtitle->addCue($mergeCue);

            if ($this->options->topBottomMode()) {
                $addedCue->stylePositionTop();
            }
        }

        return $baseSubtitle
            ->removeEmptyCues()
            ->removeDuplicateCues();
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

            // If there is no nearby cue, merge the whole cue.
            is_null($nearestCue)
                ? $baseSubtitle->addCue($cue)
                : $nearestCue->addLines($cue->getLines());
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

            // Cues should be within the threshold to be a nearby cue.
            if ($difference > $this->options->nearestCueThreshold) {
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
