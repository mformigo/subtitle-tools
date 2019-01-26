<?php

namespace App\Jobs\Diagnostic;

use App\Jobs\BaseJob;
use App\Models\StoredFile;
use Illuminate\Contracts\Queue\ShouldQueue;

class CollectMetaForStoredFilesJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
        StoredFile::query()
            ->doesntHave('meta')
            ->take(1000)
            ->each(function (StoredFile $storedFile) {
                CollectStoredFileMetaJob::dispatch($storedFile);
            });
    }
}
