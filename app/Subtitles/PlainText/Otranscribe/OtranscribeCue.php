<?php

namespace App\Subtitles\PlainText\Otranscribe;

use App\Subtitles\PlainText\GenericSubtitleCue;
use App\Subtitles\TransformsToGenericCue;

class OtranscribeCue extends GenericSubtitleCue implements TransformsToGenericCue
{
    public function __construct($string)
    {
        if (! static::isTimingString($string)) {
            throw new \Exception('Not a valid '.get_class($this).' cue string: '.$string);
        }

        // For some reason, oTranscribe uses "&nbsp;" as spaces
        $string = str_replace(html_entity_decode('&nbsp;'), ' ', $string);

        if (! str_contains($string, ' ')) {
            $string .= ' ';
        }

        [$timing, $text] = explode(' ', $string, 2);

        $this->addLine($text);

        [$minutes, $seconds] = explode(':', $timing);

        $startMs = ($minutes * 60 + $seconds) * 1000;

        $this->setTiming($startMs, $startMs);
    }

    public function toGenericCue(): GenericSubtitleCue
    {
        return (new GenericSubtitleCue)
            ->setLines($this->lines)
            ->setTiming($this->startMs, $this->endMs);
    }

    public static function isTimingString($string)
    {
        // one of those spaces is a "&nbsp;"
        return (bool) preg_match('/^\d\d:\d\d( | |$)/', $string);
    }
}
