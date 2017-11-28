<?php

namespace App\Console\Commands\Janitor;

use App\Models\FileGroup;
use Illuminate\Console\Command;

class DeleteFileJobs extends Command
{
    protected $signature = 'st:prune-file-jobs';

    protected $description = 'Prune all file groups and file jobs older than one week';

    public function handle()
    {
        $this->comment('Deleting file groups older than one week...');

        $deleteOlderThan = now()->subDays(7);

        // The left-over stored files are deleted daily by the PruneStoredFiles command
        $rowsAffected = FileGroup::query()
            ->whereDate('created_at', '<', $deleteOlderThan)
            ->delete();

        $this->info('Done! '.$rowsAffected.' file groups deleted');
    }
}
