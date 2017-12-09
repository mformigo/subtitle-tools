<?php

namespace App\Console\Commands\Janitor;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneSubIdxFiles extends Command
{
    protected $signature = 'st:prune-sub-idx-files';

    protected $description = 'Deletes sub/idx files older than a few days';

    public function handle()
    {
        $deleteDirectoryIfNotEndsWith = [
            DIRECTORY_SEPARATOR.now()->format('Y-z'),
            DIRECTORY_SEPARATOR.now()->subDays(1)->format('Y-z'),
            DIRECTORY_SEPARATOR.now()->subDays(2)->format('Y-z'),
            DIRECTORY_SEPARATOR.now()->subDays(3)->format('Y-z'),
        ];

        $subIdxDirectory = Storage::directories('sub-idx/');

        collect($subIdxDirectory)
            ->filter(function ($name) use ($deleteDirectoryIfNotEndsWith) {
                return ! ends_with($name, $deleteDirectoryIfNotEndsWith);
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
