<?php

namespace App\Models\Traits;

use Carbon\Carbon;

trait MeasuresQueueTime
{
    public function measureStart()
    {
        $createdAt = new Carbon($this->created_at);

        $start = Carbon::now();

        $this->started_at = $start;
        $this->queue_time = $start->diffInSeconds($createdAt);
    }

    public function measureEnd()
    {
        $finishedAt = Carbon::now();

        $startedAt = new Carbon($this->started_at);

        $this->finished_at = $finishedAt;
        $this->work_time   = $finishedAt->diffInSeconds($startedAt);
    }
}
