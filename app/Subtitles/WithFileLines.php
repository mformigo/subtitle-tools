<?php

namespace App\Subtitles;

trait WithFileLines
{
    protected $lines = [];

    public function getContent()
    {
        return implode("\r\n", $this->getContentLines());
    }

    public function getContentLines()
    {
        return $this->lines;
    }
}
