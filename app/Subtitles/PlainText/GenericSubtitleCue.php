<?php

namespace App\Subtitles\PlainText;

class GenericSubtitleCue
{
    protected $lines = [];

    protected $startMs = 0;

    protected $endMs = 0;

    public function getLines()
    {
        return $this->lines;
    }

    public function setLines(array $lines)
    {
        $this->lines = [];

        foreach($lines as $line) {
            $this->addLine($line);
        }

        return $this;
    }

    public function addLine($line)
    {
        // Smi files commonly use nbsp (with and without a semicolon) to make blank lines
        $line = trim(
            str_ireplace(['&nbsp;', '&nbsp'], " ", $line)
        );

        if(!empty($line)) {
            $this->lines[] = $line;
        }

        return $this;
    }

    public function hasLines()
    {
        return count($this->lines) > 0;
    }

    public function setTiming($startMs, $endMs)
    {
        if($startMs > $endMs) {
            throw new \Exception("EndMs must be the same or larger than StartMs");
        }

        $this->startMs = ($startMs < 0) ? 0 : $startMs;

        $this->endMs = ($endMs < 0) ? 0 : $endMs;

        return $this;
    }

    public function getStartMs()
    {
        return $this->startMs;
    }

    public function getEndMs()
    {
        return $this->endMs;
    }

    public function shift($ms)
    {
        $this->setTiming(
            $this->startMs + $ms,
            $this->endMs + $ms
        );

        return $this;
    }
}
