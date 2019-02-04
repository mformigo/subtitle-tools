<?php

namespace App\Jobs\Janitor;

use App\Jobs\BaseJob;
use App\Models\SupJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class PruneSupJobsJob extends BaseJob implements ShouldQueue
{
    /**
     * Set the input "stored_file_id" of old sup jobs to null. We can still use
     * the "ocr_language" in combination with the "input_file_hash" to retrieve
     * previously processed sup jobs from the database.
     */
    public function handle()
    {
        $threshold = now()->subDays(7);

        // The left-over stored files are deleted by the PruneStoredFiles command
        SupJob::query()
            ->where('created_at', '<', $threshold)
            ->update(['input_stored_file_id' => null]);
    }
}
