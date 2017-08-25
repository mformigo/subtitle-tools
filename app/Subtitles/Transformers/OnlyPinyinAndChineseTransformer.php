<?php

namespace App\Subtitles\Transformers;

use App\Subtitles\ContainsGenericCues;
use App\Utils\Pinyin\Pinyin;

class OnlyPinyinAndChineseTransformer implements CueTransformer
{
    protected $pinyin;

    public function __construct(Pinyin $pinyin)
    {
        $this->pinyin = $pinyin;
    }

    /**
     * Adds pinyin underneath every line that has Chinese, removes all lines that don't have Chinese
     * @param ContainsGenericCues $subtitle
     * @return bool False if no valid transformations have happened, true otherwise
     */
    public function transformCues(ContainsGenericCues $subtitle)
    {
        $hasChangedSomething = false;

        foreach($subtitle->getCues(false) as $cue) {
            $cue->alterLines(function($line, $index) use (&$hasChangedSomething) {
                $converted = $this->pinyin->convert($line);

                if($converted === $line) {
                    return '';
                }

                $hasChangedSomething = true;

                return $line . "\n" . $converted;
            });
        }

        return $hasChangedSomething;
    }
}
