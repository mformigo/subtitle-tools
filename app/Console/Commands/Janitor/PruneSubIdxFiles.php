<?php

namespace App\Console\Commands\Janitor;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneSubIdxFiles extends Command
{
    protected $signature = 'st:prune-sub-idx-files';

    protected $description = 'Deletes sub/idx files older than two weeks';

    public function handle()
    {
        $notEndWith = [
            DIRECTORY_SEPARATOR.date('Y-W'),
            DIRECTORY_SEPARATOR.date('Y-W', strtotime('-7 days')),
        ];

        $subIdxDirectory = Storage::directories('sub-idx/');

        collect($subIdxDirectory)
            ->filter(function ($name) use ($notEndWith) {
                return ! ends_with($name, $notEndWith);
            })
            ->tap(function ($directoryNames) {
                $this->info('Found '.count($directoryNames).' old sub/idx directories');
            })
            ->each(function ($directoryName) {
                $this->comment('Removing: '. $directoryName);

                Storage::deleteDirectory($directoryName);
            });

        $this->call('st:calculate-disk-usage');
    }
}
