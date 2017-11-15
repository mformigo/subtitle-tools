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
        if(! app()->isDownForMaintenance()) {
            $this->error('Can only clean disk in maintenance mode');

            return;
        }

        // The left-over stored files are deleted daily by the PruneStoredFiles command

        $this->info('Truncating file jobs and file groups...');

        FileJob::truncate();

        FileGroup::truncate();

        $this->call('st:calculate-disk-usage');

        $this->info('Don\'t forget to run php artisan up!');
        $this->info('Don\'t forget to run php artisan up!!');
        $this->info('Don\'t forget to run php artisan up!!!');
    }
}
