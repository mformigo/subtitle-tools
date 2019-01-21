<?php

namespace App\Console\Commands\Janitor;

use App\Models\SupJob;
use Illuminate\Console\Command;

class PruneSupJobs extends Command
{
    protected $signature = 'st:prune-sup-files';

    protected $description = 'Prune sup files older than a few days';

    /**
     * Set the input "stored_file_id" of old sup jobs to null. We can still use
     * the "ocr_language" in combination with the "input_file_hash" to retrieve
     * previously processed sup jobs from the database.
     */
    public function handle()
    {
        $this->comment('Deleting sup files older than a few days...');

        $deleteOlderThan = now()->subDays(7);

        // The left-over stored files are deleted by the PruneStoredFiles command
        $rowsAffected = SupJob::query()
            ->where('created_at', '<', $deleteOlderThan)
            ->update(['input_stored_file_id' => null]);

        $this->info('Done! '.$rowsAffected.' sup files deleted');
    }
}
