<?php

namespace App\Jobs\Janitor;

use App\Jobs\BaseJob;
use App\Jobs\Diagnostic\CalculateDiskUsageJob;
use App\Models\SubIdx;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class PruneSubIdxFilesJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
        $baseDirectories = Storage::directories('sub-idx/');

        $subIdxDirectories = collect(Storage::directories('sub-idx/', true))
            ->diff($baseDirectories)
            ->map(function ($string) {
                return $string.DIRECTORY_SEPARATOR;
            })
            ->toArray();

        $databaseStorageDirectories = SubIdx::pluck('store_directory')->toArray();

        $orphanedDirectories = array_diff($subIdxDirectories, $databaseStorageDirectories);

        foreach ($orphanedDirectories as $dir) {
            Storage::deleteDirectory($dir);
        }

        foreach ($baseDirectories as $dir) {
            if (Storage::files($dir, true) === []) {
                Storage::deleteDirectory($dir);
            }
        }

        CalculateDiskUsageJob::dispatch();
    }
}
