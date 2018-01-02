<?php

namespace App\Subtitles\PlainText;

use SjorsO\TextFile\Facades\TextFileReader;
use App\Subtitles\ShiftsCues;
use App\Subtitles\TextFile;
use App\Subtitles\TransformsToGenericSubtitle;
use App\Subtitles\WithFileLines;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Smi extends TextFile implements TransformsToGenericSubtitle, ShiftsCues
{
    use WithFileLines;

    protected $extension = "smi";

    protected $cues = [];

    /**
     * @return GenericSubtitle
     */
    public function toGenericSubtitle()
    {
        $genericSubtitle = new GenericSubtitle();

        $genericSubtitle->setFilePath($this->filePath);

        $genericSubtitle->setFileNameWithoutExtension($this->originalFileNameWithoutExtension);

        // Smi files ignore new lines, the easiest way to parse the file is by removing new lines.
        $cleanContent = implode('', $this->lines);

        if (stripos($cleanContent, "<sync ") === false) {
            return $genericSubtitle;
        }

        $fromIndex = stripos($cleanContent, "<sync ");

        while ($fromIndex !== false && $fromIndex < strlen($cleanContent))
        {
            $toIndex = stripos($cleanContent, "<sync ", $fromIndex + 1);

            if ($toIndex === false) {
                $toIndex = strlen($cleanContent);
            }

            $maybeCueText = substr($cleanContent, $fromIndex, $toIndex - $fromIndex);

            if (preg_match('/^<sync .*?start=(?:"?|\'?)(?<startMs>\d+).*?>/i', $maybeCueText, $startTag))
            {
                $startMs = $startTag['startMs'];
                $endMs   = $startTag['startMs'];

                if (preg_match('/^<sync .*?end=(?:"?|\'?)(?<endMs>\d+).*?>/i', $maybeCueText, $endTag)) {
                    $endMs = $endTag['endMs'];
                }

                // Smi cues end when the next cue starts, except when they have an end= attribute
                if ($genericSubtitle->hasCues()) {
                    $previousCue = $genericSubtitle->getCues(false)[count($genericSubtitle->getCues(false)) - 1];

                    if ($previousCue->getStartMs() === $previousCue->getEndMs()) {
                        if ($previousCue->getStartMs() <= $startMs) {
                            $previousCue->setTiming($previousCue->getStartMs(), $startMs);
                        }
                    }
                }

                if ($startMs <= $endMs) {
                    $genericCue = new GenericSubtitleCue();

                    $genericCue->setTiming($startMs, $endMs);

                    $genericCue->setLines(preg_split("~<br>|<br/>|<br />~i", $maybeCueText));

                    $genericSubtitle->addCue($genericCue);
                }
            }

            $fromIndex = $toIndex;
        }

        // The last cue in the file needs to have its end time set manually (if it did not have an end= attribute)
        if ($genericSubtitle->hasCues()) {
            $lastCue = $genericSubtitle->getCues(false)[count($genericSubtitle->getCues(false)) - 1];

            if ($lastCue->getStartMs() === $lastCue->getEndMs()) {
                $lastCue->setTiming($lastCue->GetStartMs(), $lastCue->GetStartMs() + 3000);
            }
        }

        $genericSubtitle->stripAngleBracketsFromCues();

        return $genericSubtitle;
    }

    public static function isThisFormat($file)
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        $content = TextFileReader::getContent($filePath);

        $hasSamiTag = stripos($content, '<sami>') !== false;

        if ($hasSamiTag) {
            // If there is a <sami> tag, anything resembling a cue is good enough
            return stripos($content, '<sync ') !== false;
        }

        return preg_match('/<sync .*?start=(?:"?|\'?)(\d+).*?>/i', $content);
    }

    public function shift($ms)
    {
        // Smi files don't support partial shifts
        // It would be a lot of work because the EndMs is often decided by the next cues StartMs

        if ($ms == 0) {
            return $this;
        }

        // a line can contain multiple sync tags
        $this->lines = array_map(function ($line) use ($ms) {
            if (stripos($line, "<sync ") === false) {
                return $line;
            }

            $parts = array_map(function ($part) use ($ms) {
                if (!preg_match('/<sync .*?start=.*$/i', $part)) {
                    return $part;
                }

                $syncTags = array_map(function ($syncTag) use ($ms) {
                    if (stripos($syncTag, 'sync ') !== 0) {
                        return $syncTag;
                    }

                    return preg_replace_callback('/\d+/', function ($matches) use ($ms) {
                        $newNumber = (int)$matches[0] + $ms;

                        return ($newNumber < 0) ? "0" : (string)$newNumber;
                    }, $syncTag);

                }, explode('<', $part));

                return implode('<', $syncTags);
            }, explode('>', $line));

            return implode('>', $parts);
        }, $this->lines);

        return $this;
    }
}
