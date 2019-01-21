<?php

namespace App\Jobs\Janitor;

use App\Jobs\BaseJob;
use App\Models\SubIdx;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;

class PruneSubIdxTableJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
        $threshold = now()->subDays(45);

        SubIdx::query()
            ->where('created_at', '<', $threshold)
            ->where(function (Builder $query) use ($threshold) {
                $query->whereNull('last_cache_hit')->orWhere('last_cache_hit', '<', $threshold);
            })
            ->delete();
    }
}
