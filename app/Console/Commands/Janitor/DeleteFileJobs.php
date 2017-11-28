<?php

namespace App\Console\Commands\Janitor;

use App\Models\FileGroup;
use Illuminate\Console\Command;

class DeleteFileJobs extends Command
{
    protected $signature = 'st:delete-file-jobs';

    protected $description = 'Truncates all file groups and file jobs';

    public function handle()
    {
        $this->info('Truncating file groups...');

        // The left-over stored files are deleted daily by the PruneStoredFiles command
        FileGroup::truncate();
    }
}
