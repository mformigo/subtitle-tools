<?php

namespace App\Console\Commands\Janitor;

use App\Models\FileGroup;
use Illuminate\Console\Command;

class PruneFileJobs extends Command
{
    protected $signature = 'st:prune-file-jobs';

    protected $description = 'Prune all file groups and file jobs older than a few days';

    public function handle()
    {
        $this->comment('Deleting file groups older than a few days...');

        $deleteOlderThan = now()->subDays(3);

        // The left-over stored files are deleted daily by the PruneStoredFiles command
        $rowsAffected = FileGroup::query()
            ->where('created_at', '<', $deleteOlderThan)
            ->delete();

        $this->info('Done! '.$rowsAffected.' file groups deleted');
    }
}
