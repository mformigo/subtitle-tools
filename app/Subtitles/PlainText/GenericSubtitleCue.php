<?php

namespace App\Subtitles\PlainText;

use Closure;
use RuntimeException;

class GenericSubtitleCue
{
    protected $lines = [];

    protected $startMs = 0;

    protected $endMs = 0;

    public function getLines()
    {
        return $this->lines;
    }

    public function getLinesAsText()
    {
        return implode("\n", $this->lines);
    }

    public function setLines(array $lines)
    {
        $this->lines = [];

        foreach ($lines as $line) {
            $this->addLine($line);
        }

        return $this;
    }

    public function addLine($line)
    {
        // Smi files commonly use nbsp (with and without a semicolon) to make blank lines
        $line = str_ireplace(['&nbsp;', '&nbsp'], ' ', $line);

        $line = trim($line);

        // Don't add a line if it only contains these characters.
        //   A line with only a dash is often a left-over when stripping brackets from hearing-impaired subtitles, eg: '- [men screaming]'
        //   Hearing-impaired subtitles often include lines with only asterisks
        //   Stripping brackets from hearing-impaired subtitles can leave lines like this: '- -'
        if (! str_replace(['-', ' ', '*'], '', $line)) {
            return $this;
        }

        if (! empty($line)) {
            $this->lines[] = $line;
        }

        return $this;
    }

    public function addLines($lines)
    {
        foreach ($lines as $line) {
            $this->addLine($line);
        }

        return $this;
    }

    public function hasLines()
    {
        return count($this->lines) > 0;
    }

    public function setTiming($startMs, $endMs)
    {
        if ($startMs > $endMs) {
            throw new RuntimeException("Cue can't end before it starts ('{$endMs}' > '{$startMs}')");
        }

        $this->startMs = ($startMs < 0) ? 0 : (int) $startMs;

        $this->endMs = ($endMs < 0) ? 0 : (int) $endMs;

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
        if (! preg_match('/^(-\d+|\d+)$/', $ms)) {
            throw new RuntimeException('Invalid shift amount: '.$ms);
        }

        $this->setTiming(
            $this->startMs + $ms,
            $this->endMs + $ms
        );

        return $this;
    }

    public function toArray()
    {
        $lines = ["# Start={$this->getStartMs()}# End={$this->getStartMs()}"];

        foreach ($this->lines as $line) {
            $lines[] = $line;
        }

        $lines[] = "";

        return $lines;
    }

    /**
     * Pass all lines from this cue one by one into a closure. The closure must return
     * a string. The returned string can contain new lines.
     *
     * @param Closure $closure
     *
     * @return $this
     */
    public function alterLines(Closure $closure)
    {
        $alteredLines = [];

        for ($i = 0; $i < count($this->lines); $i++) {
            $alteredLines = array_merge(
                $alteredLines,
                explode("\n", $closure($this->lines[$i], $i))
            );
        }

        return $this->setLines($alteredLines);
    }

    /**
     * Pass all lines from this cue as an array into a closure. The closure must return
     * an array
     *
     * @param Closure $closure
     *
     * @return $this
     */
    public function alterAllLines(Closure $closure)
    {
        $alteredLines = $closure($this->lines);

        return $this->setLines($alteredLines);
    }

    /**
     * Style this cue to appear on the top of the screen.
     *
     * @return $this
     */
    public function stylePositionTop()
    {
        return $this;
    }

    /**
     * Used for cue comparison (like removeDuplicateCues())
     *
     * @return string
     */
    public function __toString()
    {
        return "{{$this->startMs}}{{$this->endMs}}" . json_encode($this->lines);
    }
}
