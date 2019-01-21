<?php

namespace App\Jobs;

use App\Models\SupJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;

class RandomizeSupUrlKeysJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
        $notUpdatedSince = now()->subHours(36);

        $noCacheHitSince = now()->subMinutes(20);

        SupJob::query()
            ->where('updated_at', '<', $notUpdatedSince)
            ->where(function (Builder $query) use ($noCacheHitSince) {
                $query->whereNull('last_cache_hit')->orWhere('last_cache_hit', '<', $noCacheHitSince);
            })
            ->each(function (SupJob $supJob) {
                $supJob->update(['url_key' => generate_url_key()]);
            });
    }
}
