<?php

namespace App\Jobs\Diagnostic;

use App\Jobs\BaseJob;
use App\Models\Diagnostic\FileJobStats;
use App\Models\FileGroup;
use App\Models\FileJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class CollectFileJobStatsJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
        $yesterday = now()->subDay(1)->format('Y-m-d');

        $exists = FileJobStats::query()
            ->where('date', $yesterday)
            ->exists();

        if ($exists) {
            return;
        }

        $totalStats = FileJobStats::make([
            'date' => $yesterday,
            'tool_route' => '*',
            'times_used' => 0,
            'total_files' => 0,
            'amount_failed' => 0,
            'total_size' => 0,
        ]);

        foreach (config('st.tool_routes') as $toolRoute) {
            $fileJobStats = $this->collectStats($toolRoute, $yesterday);

            $totalStats->times_used += $fileJobStats->times_used;
            $totalStats->total_files += $fileJobStats->total_files;
            $totalStats->amount_failed += $fileJobStats->amount_failed;
            $totalStats->total_size += $fileJobStats->total_size;
        }

        $totalStats->save();
    }

    private function collectStats($toolRoute, $forDate)
    {
        $fileGroups = FileGroup::query()
            ->where('tool_route', $toolRoute)
            ->whereDate('created_at', $forDate)
            ->with('fileJobs', 'fileJobs.inputStoredFile', 'fileJobs.inputStoredFile.meta')
            ->get();

        $totalFiles = 0;
        $amountFailed = 0;
        $totalSize = 0;

        foreach ($fileGroups as $fileGroup) {
            $totalFiles += $fileGroup->fileJobs->count();

            // The merge tool has two input files per file job
            if ($toolRoute === 'merge') {
                $totalFiles += $fileGroup->fileJobs->count();
            }

            foreach ($fileGroup->fileJobs as $fileJob) {
                // TODO: sometimes, because of a missing foreign key, input stored files do not exist.
                if (! $fileJob->inputStoredFile) {
                    continue;
                }

                if ($fileJob->inputStoredFile->meta) {
                    $totalSize += $fileJob->inputStoredFile->meta->size;
                }
            }

            $amountFailed += $fileGroup->fileJobs->filter(function (FileJob $fileJob) {
                return $fileJob->error_message !== null;
            })->count();
        }

        return FileJobStats::create([
            'date' => $forDate,
            'tool_route' => $toolRoute,
            'times_used' => count($fileGroups),
            'total_files' => $totalFiles,
            'amount_failed' => $amountFailed,
            'total_size' => $totalSize,
        ]);
    }
}
