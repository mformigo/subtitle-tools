<?php

namespace App\Jobs\Janitor;

use App\Jobs\BaseJob;
use App\Jobs\Diagnostic\CalculateDiskUsageJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class PruneSubIdxFilesJob extends BaseJob implements ShouldQueue
{
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
            ->each(function ($directoryName) {
                Storage::deleteDirectory($directoryName);
            });

        CalculateDiskUsageJob::dispatch();
    }
}
