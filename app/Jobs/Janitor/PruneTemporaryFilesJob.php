<?php

namespace App\Jobs\Janitor;

use App\Jobs\BaseJob;
use App\Jobs\Diagnostic\CalculateDiskUsageJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class PruneTemporaryFilesJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
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

        CalculateDiskUsageJob::dispatch();
    }

    private function cleanTemporaryFiles($dontDeletePrefixes)
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

    private function cleanTemporaryDirectories($dontDeletePrefixes)
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
