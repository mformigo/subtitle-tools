<?php

namespace App\Subtitles\PlainText;

class SrtTiming
{
    protected $start;

    protected $end;

    public function __construct(string $timing)
    {
        $timing = strtolower(
            trim($timing)
        );

        if (! preg_match("/^.+? -?-> .+?( ? x1: ?\d+ x2: ?\d+ y1: ?\d+ y2: ?\d+|)$/", $timing)) {
            return;
        }

        $timing = trim(
            str_before($timing, 'x')
        );

        [$startTimecode, $endTimecode] = preg_split('/ -?-> /', $timing);

        $this->start = new SrtTimecode($startTimecode);

        $this->end = new SrtTimecode($endTimecode);
    }

    public function valid()
    {
        if (! $this->start || ! $this->end) {
            return false;
        }

        if ($this->start->invalid() || $this->end->invalid()) {
            return false;
        }

        return $this->start->milliseconds() <= $this->end->milliseconds();
    }

    public function invalid()
    {
        return ! $this->valid();
    }

    public function start(): SrtTimecode
    {
        return $this->start;
    }

    public function end(): SrtTimecode
    {
        return $this->end;
    }
}
