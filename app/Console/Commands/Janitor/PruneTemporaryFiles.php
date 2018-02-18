<?php

namespace App\Console\Commands\Janitor;

use Illuminate\Console\Command;

class PruneTemporaryFiles extends Command
{
    protected $signature = 'st:prune-temporary-files';

    protected $description = 'Prune temporary files and directories older than five days';

    public function handle()
    {
        $this->info('Deleting temporary files and directories older than five days...');

        // Slow OCR jobs could take more than a day if the queue is very busy,
        // to be safe, only delete things older than five days.
        $dontDeletePrefixes = [
            date('Y-z-'), // todays prefix
            date('Y-z-', strtotime('-1 days')),
            date('Y-z-', strtotime('-2 days')),
            date('Y-z-', strtotime('-3 days')),
            date('Y-z-', strtotime('-4 days')),
        ];

        $this->cleanTemporaryFiles($dontDeletePrefixes);

        $this->cleanTemporaryDirectories($dontDeletePrefixes);

        $this->call('st:calculate-disk-usage');
    }

    protected function cleanTemporaryFiles($dontDeletePrefixes)
    {
        $temporaryFilesDir = storage_disk_file_path('temporary-files/');

        $fileNames = scandir($temporaryFilesDir);

        $fileNames = array_filter($fileNames, function ($name) use ($dontDeletePrefixes) {
            return ! starts_with($name, '.') && ! starts_with($name, $dontDeletePrefixes);
        });

        foreach ($fileNames as $name) {
            unlink($temporaryFilesDir.$name);
        }
    }

    protected function cleanTemporaryDirectories($dontDeletePrefixes)
    {
        $temporaryDirectoriesDirectory = storage_disk_file_path('temporary-dirs/');

        $dirPaths = glob($temporaryDirectoriesDirectory.'*', GLOB_ONLYDIR);

        $dirPaths = array_filter($dirPaths, function ($name) use ($dontDeletePrefixes) {
            foreach ($dontDeletePrefixes as $prefix) {
                if (str_contains($name, DIRECTORY_SEPARATOR.$prefix)) {
                    return false;
                }
            }

            return true;
        });

        foreach ($dirPaths as $path) {
            $globPattern = str_finish($path, '/').'*';

            foreach (glob($globPattern) as $filePath) {
                unlink($filePath);
            }

            rmdir($path);
        }
    }
}
