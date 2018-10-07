<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\LoadsGenericCues;
use App\Subtitles\TimingStrings;
use Exception;
use LogicException;

class SrtCue extends GenericSubtitleCue implements TimingStrings, LoadsGenericCues
{
    public function __construct($source = null)
    {
        if ($source === null) {
            return;
        } elseif ($source instanceof GenericSubtitleCue) {
            $this->loadGenericCue($source);
        } else {
            throw new \InvalidArgumentException("Invalid SrtCue source");
        }
    }

    public function setTimingFromString($string)
    {
        $timing = new SrtTiming($string);

        if ($timing->invalid()) {
            throw new Exception('Not a valid timing string: '.$string);
        }

        $this->setTiming(
            $timing->start()->milliseconds(),
            $timing->end()->milliseconds()
        );

        return $this;
    }

    public function getTimingString()
    {
        $start = new SrtTimecode($this->startMs);

        $end = new SrtTimecode($this->endMs);

        return $start->timecode().' --> '.$end->timecode();
    }

    public function loadGenericCue(GenericSubtitleCue $genericCue)
    {
        $this->setTiming(
            $genericCue->getStartMs(),
            $genericCue->getEndMs()
        );

        $this->setLines($genericCue->getLines());

        return $this;
    }

    public function stylePositionTop()
    {
        if (count($this->lines) === 0) {
            throw new LogicException('A cue with no lines cannot be styled');
        }

        $this->lines[0] = str_start($this->lines[0], '{\an8}');

        return $this;
    }

    public function toArray()
    {
        $lines = [$this->getTimingString()];

        foreach ($this->lines as $line) {
            $lines[] = $line;
        }

        $lines[] = "";

        return $lines;
    }

    public static function isTimingString($string)
    {
        $timing = new SrtTiming($string);

        return $timing->valid();
    }
}
