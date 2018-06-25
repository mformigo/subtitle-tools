<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\ContainsGenericCues;
use App\Subtitles\LoadsGenericSubtitles;
use App\Subtitles\PartialShiftsCues;
use App\Subtitles\ShiftsCues;
use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\Watermarkable;
use App\Subtitles\WithFileLines;
use App\Subtitles\WithGenericCues;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Srt extends TextFile implements LoadsGenericSubtitles, ShiftsCues, PartialShiftsCues, Watermarkable, ContainsGenericCues, TransformsToGenericSubtitle
{
    use WithFileLines, WithGenericCues;

    protected $extension = 'srt';

    /**
     * @var SrtCue[]
     */
    protected $cues = [];

    /**
     * @param $cue
     *
     * @return SrtCue
     */
    public function addCue($cue)
    {
        if (! $cue instanceof SrtCue && ! $cue instanceof GenericSubtitleCue) {
            throw new \InvalidArgumentException('Invalid cue');
        }

        $cue = $cue instanceof SrtCue
            ? $cue
            : new SrtCue($cue);

        $this->cues[] = $cue;

        return $cue;
    }

    public function getContentLines()
    {
        $id = 1;
        $lines = [];

        foreach ($this->getCues() as $cue) {
            $lines[] = (string) $id++;

            $lines = array_merge($lines, $cue->toArray());
        }

        return $lines;
    }

    public static function isThisFormat($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $lines = app(\SjorsO\TextFile\Contracts\TextFileReaderInterface::class)->getLines($filePath);

        for ($i = 1; $i < count($lines); $i++) {
            if (SrtCue::isTimingString($lines[$i]) && preg_match('/^\d+$/', trim($lines[$i-1]))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $file string|UploadedFile A file path or UploadedFile
     * @return $this
     */
    public function loadFile($file)
    {
        $name = $file instanceof UploadedFile ? $file->getClientOriginalName() : $file;

        $this->originalFileNameWithoutExtension = pathinfo($name, PATHINFO_FILENAME);

        $this->filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $lines = app(\SjorsO\TextFile\Contracts\TextFileReaderInterface::class)->getLines($this->filePath);

        $this->cues = [];

        // ensure parsing works properly on files missing the required trailing empty line
        $lines[] = "";

        $timingIndexes = [];

        for ($i = 0; $i < count($lines); $i++) {
            if (SrtCue::isTimingString($lines[$i])) {
                $timingIndexes[] = $i;
            }
        }

        $timingIndexes[] = count($lines);

        for ($timingIndex = 0; $timingIndex < count($timingIndexes) - 1; $timingIndex++) {
            $newCue = new SrtCue();

            $newCue->setTimingFromString($lines[$timingIndexes[$timingIndex]]);

            for ($lineIndex = $timingIndexes[$timingIndex] + 1; $lineIndex < $timingIndexes[$timingIndex+1] - 1; $lineIndex++) {
                $newCue->addLine($lines[$lineIndex]);
            }

            $this->AddCue($newCue);
        }

        $this->removeEmptyCues()
            ->removeDuplicateCues();

        return $this;
    }

    public function loadGenericSubtitle(GenericSubtitle $genericSubtitle)
    {
        $this->setFilePath($genericSubtitle->getFilePath());

        $this->setFileNameWithoutExtension($genericSubtitle->getFileNameWithoutExtension());

        foreach ($genericSubtitle->getCues() as $genericCue) {
            $this->addCue($genericCue);
        }

        return $this;
    }

    public function shift($ms)
    {
        foreach ($this->cues as $cue) {
            $cue->shift($ms);
        }

        return $this;
    }

    public function shiftPartial($fromMs, $toMs, $ms)
    {
        if ($fromMs > $toMs || $ms == 0) {
            return $this;
        }

        foreach ($this->cues as $cue) {
            if ($cue->getStartMs() >= $fromMs && $cue->getStartMs() <= $toMs) {
                $cue->shift($ms);
            }
        }

        return $this;
    }

    public function watermark()
    {
        if (count($this->cues) === 0) {
            return $this;
        }

        $this->sortCues();

        $sampleSize = (10 < count($this->cues)) ? 10 : count($this->cues);

        for ($i = 0; $i < $sampleSize; $i++) {
            if (stripos((string)$this->cues[$i], 'subtitletools.com') !== false) {
                return $this;
            }
        }

        // 2018-05-18: A user using "Camtasia software" said that the 0 length second
        // watermark cue produces an error. Camtasia needs it to be 1 frame long
        // therefor, make it 33ms long.
        $cue = (new SrtCue)->setTiming(0, 33)->addLine('Edited at https://subtitletools.com');

        return $this->addCue($cue);
    }

    /**
     * @return GenericSubtitle
     */
    public function toGenericSubtitle()
    {
        $generic = new GenericSubtitle();

        $generic->setFilePath($this->filePath);

        $generic->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        foreach ($this->getCues(false) as $cue) {
            $newGenericCue = new GenericSubtitleCue();

            $newGenericCue->setTiming($cue->getStartMs(), $cue->getEndMs());

            $newGenericCue->setLines($cue->getLines());

            $generic->addCue($newGenericCue);
        }

        return $generic;
    }
}
