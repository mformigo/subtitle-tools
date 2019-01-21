<?php

namespace App\Jobs;

use App\Models\SubIdx;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;

class RandomizeSubIdxUrlKeysJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
        $notUpdatedSince = now()->subHours(36);

        $noCacheHitSince = now()->subMinutes(20);

        SubIdx::query()
            ->where('updated_at', '<', $notUpdatedSince)
            ->where(function (Builder $query) use ($noCacheHitSince) {
                $query->whereNull('last_cache_hit')->orWhere('last_cache_hit', '<', $noCacheHitSince);
            })
            ->each(function (SubIdx $subIdx) {
                $subIdx->update(['url_key' => generate_url_key()]);
            });
    }
}
