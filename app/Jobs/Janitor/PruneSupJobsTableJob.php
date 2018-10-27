<?php

namespace App\Jobs\Janitor;

use App\Jobs\BaseJob;
use App\Models\SupJob;
use Illuminate\Database\Eloquent\Builder;

class PruneSupJobsTableJob extends BaseJob
{
    public function handle()
    {
        $threshold = now()->subDays(60);

        SupJob::query()
            ->whereDate('created_at', '<', $threshold)
            ->where(function (Builder $query) use ($threshold) {
                $query->whereNull('last_cache_hit')->orWhereDate('last_cache_hit', '<', $threshold);
            })
            ->delete();
    }
}
