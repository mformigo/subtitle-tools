<?php

namespace App\Subtitles\Transformers;

use App\Subtitles\ContainsGenericCues;
use SjorsO\Pinyin\Pinyin;

class ChineseToPinyinTransformer implements CueTransformer
{
    protected $pinyin;

    public function __construct(Pinyin $pinyin)
    {
        $this->pinyin = $pinyin;
    }

    /**
     * Converts every line of every cue to pinyin
     *
     * @param ContainsGenericCues $subtitle
     *
     * @return bool False if no valid transformations have happened, true otherwise
     */
    public function transformCues(ContainsGenericCues $subtitle)
    {
        $hasChangedSomething = false;

        foreach ($subtitle->getCues(false) as $cue) {
            $cue->alterLines(function ($line, $index) use (&$hasChangedSomething) {
                if ($hasChangedSomething) {
                    return $this->pinyin->convert($line);
                }

                $converted = $this->pinyin->convert($line);

                if ($converted !== $line) {
                    $hasChangedSomething = true;
                }

                return $converted;
            });
        }

        return $hasChangedSomething;
    }
}
