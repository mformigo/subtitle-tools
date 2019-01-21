<?php

namespace App\Jobs\Janitor;

use App\Jobs\BaseJob;
use App\Models\SupJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;

class PruneSupJobsTableJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
        $threshold = now()->subDays(45);

        SupJob::query()
            ->where('created_at', '<', $threshold)
            ->where(function (Builder $query) use ($threshold) {
                $query->whereNull('last_cache_hit')->orWhere('last_cache_hit', '<', $threshold);
            })
            ->delete();
    }
}
