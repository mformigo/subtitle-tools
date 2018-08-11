<?php

namespace App\Support;

class ToolStats
{
    public $toolRoute;

    public $timesUsed    = 0;
    public $totalFiles   = 0;
    public $amountFailed = 0;
    public $totalSize    = 0;

    public function __construct($toolRoute)
    {
        $this->toolRoute = $toolRoute;
    }

    public function addTimesUsed($int)
    {
        $this->timesUsed += $int;
    }

    public function addTotalFiles($int)
    {
        $this->totalFiles += $int;
    }

    public function addAmountFailed($int)
    {
        $this->amountFailed += $int;
    }

    public function addTotalSize($int)
    {
        $this->totalSize += $int;
    }
}
