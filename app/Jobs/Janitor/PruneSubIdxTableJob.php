<?php

namespace App\Jobs\Janitor;

use App\Jobs\BaseJob;
use App\Models\SubIdx;
use Illuminate\Database\Eloquent\Builder;

class PruneSubIdxTableJob extends BaseJob
{
    public function handle()
    {
        $threshold = now()->subDays(45);

        SubIdx::query()
            ->whereDate('created_at', '<', $threshold)
            ->where(function (Builder $query) use ($threshold) {
                $query->whereNull('last_cache_hit')->orWhereDate('last_cache_hit', '<', $threshold);
            })
            ->delete();
    }
}
