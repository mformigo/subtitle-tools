<?php

namespace App\Subtitles\PlainText;

use Closure;

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
            throw new \Exception("Cue can't end before it starts ('{$endMs}' > '{$startMs}')");
        }

        $this->startMs = ($startMs < 0) ? 0 : (int)$startMs;

        $this->endMs = ($endMs < 0) ? 0 : (int)$endMs;

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

    public function alterLines(Closure $closure)
    {
        $alteredLines = [];

        for($i = 0; $i < count($this->lines); $i++) {
            $alteredLines = array_merge(
                $alteredLines,
                explode("\n", $closure($this->lines[$i], $i))
            );
        }

        $this->setLines($alteredLines);

        return $this;
    }

    /**
     * Used for cue comparison (like removeDuplicateCues())
     * @return string
     */
    public function __toString()
    {
        return "{{$this->startMs}}{{$this->endMs}}" . json_encode($this->lines);
    }
}
