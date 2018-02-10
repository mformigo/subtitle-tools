<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\ContainsGenericCues;
use App\Subtitles\PartialShiftsCues;
use App\Subtitles\ShiftsCues;
use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\WithFileLines;
use App\Subtitles\WithGenericCues;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Ass extends TextFile implements TransformsToGenericSubtitle, ShiftsCues, PartialShiftsCues, ContainsGenericCues
{
    use WithFileLines, WithGenericCues;

    protected $extension = 'ass';

    /**
     * @var AssCue[]
     */
    protected $cues = [];

    protected $cueClass = AssCue::class;

    /**
     * All lines until the first cue.
     */
    protected $headerLines = [];

    /**
     * @param $file string|UploadedFile
     *
     * @return $this
     */
    public function loadFile($file)
    {
        $name = $file instanceof UploadedFile
            ? $file->getClientOriginalName()
            : $file;

        $this->originalFileNameWithoutExtension = pathinfo($name, PATHINFO_FILENAME);

        $this->filePath = $file instanceof UploadedFile
            ? $file->getRealPath()
            : $file;

        $lines = read_lines($this->filePath);

        $this->cues = [];

        $firstCueIndex = -1;

        for ($i = 0; $i < count($lines); $i++) {
            if ($this->cueClass::isTimingString($lines[$i])) {
                $firstCueIndex = $i;

                break;
            }
        }

        if ($firstCueIndex === -1) {
            $this->headerLines = $lines;

            return $this;
        }

        // Save all lines from the start of the file until the first cue.
        $this->headerLines = array_slice($lines, 0, $firstCueIndex);

        for ($i = $firstCueIndex; $i < count($lines); $i++) {
            if ($this->cueClass::isTimingString($lines[$i])) {
                $this->addCue(
                    new $this->cueClass($lines[$i])
                );
            }
        }

        $this->removeEmptyCues()
            ->removeDuplicateCues();

        return $this;
    }

    public function getContentLines()
    {
        $lines = $this->headerLines;

        foreach ($this->getCues() as $cue) {
            $lines[] = $cue->toString();
        }

        if (last($lines) !== '') {
            $lines[] = '';
        }

        return $lines;
    }

    public function addCue($cue)
    {
        if ($cue instanceof AssCue) {
            $this->cues[] = $cue;
        } elseif ($cue instanceof GenericSubtitleCue) {
            $this->cues[] = new AssCue($cue);
        } else {
            throw new \InvalidArgumentException('Invalid cue');
        }

        return $this;
    }

    /**
     * @return GenericSubtitle
     */
    public function toGenericSubtitle()
    {
        $genericSubtitle = (new GenericSubtitle)
            ->setFilePath($this->filePath)
            ->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        foreach ($this->getCues() as $cue) {
            $genericSubtitle->addCue($cue);
        }

        return $genericSubtitle;
    }

    public static function isThisFormat($file)
    {
        $lines = read_lines($file);

        foreach ($lines as $line) {
            if (AssCue::isTimingString($line)) {
                return true;
            }
        }

        $maybeAssFile = false;

        $sample = array_map('strtolower', array_slice($lines, 0, 10));

        foreach ($sample as $string) {
            if (trim($string) === '[script info]') {
                $maybeAssFile = true;
                break;
            }
        }

        if ($maybeAssFile) {
            if (preg_grep("/^\[v4\+ styles\]/i" , $lines)) {
                return true;
            }
        }

        return false;
    }

    public function shift($ms)
    {
        return $this->shiftPartial(0, PHP_INT_MAX, $ms);
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

    /**
     * Removes all parentheses from the text lines, then removes empty cues
     *
     * @return $this
     */
    public function stripParenthesesFromCues()
    {
        return $this;
    }

    /**
     * Removes all angle brackets from the text lines, then removes empty cues
     *
     * @return $this
     */
    public function stripAngleBracketsFromCues()
    {
        return $this;
    }

    /**
     * Removes all curly brackets and lines containing .ass drawings from the text lines, then removes empty cues
     *
     * @return $this
     */
    public function stripCurlyBracketsFromCues()
    {
        return $this;
    }
}
