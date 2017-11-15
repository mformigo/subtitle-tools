<?php

namespace App\Console\Commands\Janitor;

use App\Models\FileGroup;
use App\Models\FileJob;
use Illuminate\Console\Command;

class DeleteFileJobs extends Command
{
    protected $signature = 'st:delete-file-jobs';

    protected $description = 'Truncates all file groups and file jobs';

    public function handle()
    {
        $this->info('Truncating file jobs and file groups...');

        if(! app()->isDownForMaintenance()) {
            $this->error('This command can only be run in maintenance mode');

            return;
        }

        // The left-over stored files are deleted daily by the PruneStoredFiles command

        FileJob::truncate();

        FileGroup::truncate();

        $this->info('Don\'t forget to run php artisan up!');
        $this->info('Don\'t forget to run php artisan up!!');
        $this->info('Don\'t forget to run php artisan up!!!');
    }
}
