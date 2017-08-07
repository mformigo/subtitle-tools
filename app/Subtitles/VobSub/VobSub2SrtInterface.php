<?php

namespace App\Subtitles\VobSub;

interface VobSub2SrtInterface
{
    public function getLanguages();

    public function extractLanguage($index);
}
