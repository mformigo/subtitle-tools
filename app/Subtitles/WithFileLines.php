<?php

namespace App\Subtitles;

trait WithFileLines
{
    protected $lines = [];

    public function getContent()
    {
        return implode("\r\n", $this->lines);
    }

    public function getContentLines()
    {
        return $this->lines;
    }
}
