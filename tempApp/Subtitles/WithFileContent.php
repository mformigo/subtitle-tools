<?php

namespace App\Subtitles;

trait WithFileContent
{
    protected $content = "";

    public function getContent()
    {
        return $this->content;
    }

    public function getContentLines()
    {
        return preg_split("/\r\n|\n|\r/", $this->getContent());
    }
}
