<?php

namespace App\Console\Commands\Janitor;

use Illuminate\Console\Command;

class PruneTemporaryFiles extends Command
{
    protected $signature = 'st:prune-temporary-files';

    protected $description = 'Prune temporary files and directories older than one day';

    public function handle()
    {
        $this->info('Deleting temporary files and directories older than one day...');

        $prefixOfToday = date('Y-z') . '-';

        $this->cleanTemporaryFiles($prefixOfToday);

        $this->cleanTemporaryDirectories($prefixOfToday);

        $this->call('st:calculate-disk-usage');
    }

    protected function cleanTemporaryFiles($prefixOfToday)
    {
        $temporaryFilesDir = storage_disk_file_path('temporary-files/');

        $fileNames = scandir($temporaryFilesDir);

        $fileNames = array_filter($fileNames, function ($name) use ($prefixOfToday) {
            return ! starts_with($name, ['.', $prefixOfToday]);
        });

        foreach ($fileNames as $name) {
            unlink($temporaryFilesDir . $name);
        }
    }

    protected function cleanTemporaryDirectories($prefixOfToday)
    {
        $temporaryDirectoriesDirectory = storage_disk_file_path('temporary-dirs/');

        $dirPaths = glob($temporaryDirectoriesDirectory.'*', GLOB_ONLYDIR);

        $dirPaths = array_filter($dirPaths, function ($name) use ($prefixOfToday) {
            return ! str_contains($name, DIRECTORY_SEPARATOR.$prefixOfToday);
        });

        foreach($dirPaths as $path) {
            $globPattern = str_finish($path, '/').'*';

            foreach(glob($globPattern) as $filePath) {
                unlink($filePath);
            }

            rmdir($path);
        }
    }
}
