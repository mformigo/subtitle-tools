<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneSubIdxFiles extends Command
{
    protected $signature = 'st:prune-sub-idx-files';

    protected $description = 'Deletes sub/idx files older than a week';

    public function handle()
    {
        $thisWeekDate = date('Y-W');

        $subIdxDirectory = Storage::directories('sub-idx/');

        collect($subIdxDirectory)
            ->filter(function ($name) use ($thisWeekDate) {
                return ! ends_with($name, '/'.$thisWeekDate);
            })
            ->tap(function ($directoryNames) {
                $this->info('Found '.count($directoryNames).' old sub/idx directories');
            })
            ->each(function ($directoryName) {
                $this->comment('Removing: '. $directoryName);

                Storage::deleteDirectory($directoryName);
            });

        $this->info('Done!');

        $this->call('st:calculate-disk-usage');
    }
}
