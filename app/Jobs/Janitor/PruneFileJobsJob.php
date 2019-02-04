<?php

namespace App\Jobs\Janitor;

use App\Jobs\BaseJob;
use App\Models\FileGroup;
use Illuminate\Contracts\Queue\ShouldQueue;

class PruneFileJobsJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
        $threshold = now()->subDays(3);

        // The left-over stored files are deleted daily by the PruneStoredFiles command
        FileGroup::query()
            ->where('created_at', '<', $threshold)
            ->delete();
    }
}
