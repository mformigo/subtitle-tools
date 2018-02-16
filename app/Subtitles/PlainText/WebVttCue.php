<?php

namespace App\Subtitles\PlainText;

use App\Subtitles\LoadsGenericCues;
use App\Subtitles\TimingStrings;
use LogicException;
use RuntimeException;

class WebVttCue extends GenericSubtitleCue implements TimingStrings, LoadsGenericCues
{
    protected $index = '';

    protected $timingStyle = '';

    public function __construct($source = null)
    {
        if ($source instanceof GenericSubtitleCue) {
            $this->loadGenericCue($source);
        } elseif ($source !== null) {
            throw new RuntimeException('Invalid VttCue source');
        }
    }

    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    public function setTimingFromString($string)
    {
        if (! self::isTimingString($string)) {
            throw new RuntimeException('Not a valid timing string: '.$string);
        }

        $string = trim($string);

        preg_match("/^(?<start>(\d{2,}:|)[0-5]\d:[0-5]\d(,|\.)\d{3}) +--> +(?<end>(\d{2,}:|)[0-5]\d:[0-5]\d(,|\.)\d{3})(?<style>| .+)$/", $string, $matches);

        $startTimecode = str_replace(',', '.', $matches['start']);
        $endTimecode   = str_replace(',', '.', $matches['end']);

        $this->setTiming(
            $this->timecodeToMs($startTimecode),
            $this->timecodeToMs($endTimecode)
        );

        $this->setTimingStyle($matches['style']);

        return $this;
    }

    public function setTimingStyle($string)
    {
        $this->timingStyle = trim($string);
    }

    public function getTimingString()
    {
        $timingStyle = empty($this->timingStyle)
            ? ''
            : ' '.$this->timingStyle;

        return $this->msToTimecode($this->startMs).' --> '.$this->msToTimecode($this->endMs).$timingStyle;
    }

    private function msToTimecode($ms)
    {
        if ($ms < 0) {
            return "00:00:00.000";
        }

        $SS = floor($ms / 1000);
        $MM = floor($SS / 60);
        $HH = floor($MM / 60);
        $MIL = $ms % 1000;
        $SS = $SS % 60;
        $MM = $MM % 60;

        $HH  = str_pad($HH,  2, "0", STR_PAD_LEFT);
        $MM  = str_pad($MM,  2, "0", STR_PAD_LEFT);
        $SS  = str_pad($SS,  2, "0", STR_PAD_LEFT);
        $MIL = str_pad($MIL, 3, "0", STR_PAD_LEFT);

        return "{$HH}:{$MM}:{$SS}.{$MIL}";
    }

    private function timecodeToMs($timecode)
    {
        $parts = preg_split("/(:|\.)/", $timecode);

        if (count($parts) === 4) {
            list($HH, $MM, $SS, $MIL) = $parts;
        }
        else {
            $HH = 0;
            list($MM, $SS, $MIL) = $parts;
        }

        return ($HH * 60 * 60 * 1000) +
               ($MM      * 60 * 1000) +
               ($SS           * 1000) +
               ($MIL                );
    }

    public function toArray()
    {
        $lines = [];

        if (! blank($this->index)) {
            $lines[] = $this->index;
        }

        $lines[] = $this->getTimingString();

        foreach ($this->lines as $line) {
            $lines[] = $line;
        }

        $lines[] = '';

        return $lines;
    }

    public static function isTimingString($string)
    {
        $string = trim($string);

        if (! preg_match("/^(?<start>(\d{2,}:|)[0-5]\d:[0-5]\d(,|\.)\d{3}) +--> +(?<end>(\d{2,}:|)[0-5]\d:[0-5]\d(,|\.)\d{3})(?<style>| .+)$/", $string, $matches)) {
            return false;
        }

        if (strpos($matches['style'], '-->') !== false) {
            return false;
        }

        $start = str_replace([':', ',', '.'], '', $matches['start']);
        $end   = str_replace([':', ',', '.'], '', $matches['end']);

        if ($start > $end) {
            return false;
        }

        return true;
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
}
