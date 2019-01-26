<?php

namespace App\Jobs\Diagnostic;

use App\Jobs\BaseJob;
use App\Models\SupJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class CollectMetaForSupJobsJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
        SupJob::query()
            ->whereNotNull('finished_at')
            ->doesntHave('meta')
            ->take(50)
            ->each(function (SupJob $supJob) {
                CollectSupMetaJob::dispatch($supJob);
            });
    }
}
