<?php

namespace App\Models\Traits;

use Carbon\Carbon;

trait MeasuresQueueTime
{
    public function measureStart()
    {
        $createdAt = new Carbon($this->created_at);

        $start = Carbon::now();

        $this->update([
            'started_at' => $start,
            'queue_time' => $start->diffInSeconds($createdAt),
        ]);
    }

    public function measureEnd()
    {
        $finishedAt = Carbon::now();

        $startedAt = new Carbon($this->started_at);

        $this->update([
            'finished_at' => $finishedAt,
            'work_time'   => $finishedAt->diffInSeconds($startedAt),
        ]);
    }
}
