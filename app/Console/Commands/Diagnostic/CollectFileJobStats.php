<?php

namespace App\Console\Commands\Diagnostic;

use App\Models\Diagnostic\FileJobStats;
use App\Models\FileGroup;
use App\Models\FileJob;
use Illuminate\Console\Command;

class CollectFileJobStats extends Command
{
    protected $signature = 'st:collect-file-job-stats';

    protected $description = 'Collect file job stats for yesterday';

    public function handle()
    {
        $toolRoutes = config('st.tool_routes');

        $yesterday = now()->subDay(1)->format('Y-m-d');

        $totalStats = FileJobStats::make([
            'date'          => $yesterday,
            'tool_route'    => '*',
            'times_used'    => 0,
            'total_files'   => 0,
            'amount_failed' => 0,
            'total_size'    => 0,
        ]);

        foreach ($toolRoutes as $toolRoute) {
            $fileJobStats = $this->collectStats($toolRoute, $yesterday);

            $totalStats->times_used    += $fileJobStats->times_used;
            $totalStats->total_files   += $fileJobStats->total_files;
            $totalStats->amount_failed += $fileJobStats->amount_failed;
            $totalStats->total_size    += $fileJobStats->total_size;
        }

        $totalStats->save();
    }

    protected function collectStats($toolRoute, $forDate)
    {
        $fileGroups = FileGroup::query()
            ->where('tool_route', $toolRoute)
            ->whereDate('created_at', $forDate)
            ->with('fileJobs')
            ->with('fileJobs.inputStoredFile')
            ->with('fileJobs.inputStoredFile.meta')
            ->get();

        $timesUsed = count($fileGroups);
        $totalFiles = 0;
        $amountFailed = 0;
        $totalSize = 0;

        foreach ($fileGroups as $fileGroup) {
            $totalFiles += $fileGroup->fileJobs->count();

            // The merge tool always has two input files but only one file job.
            if ($toolRoute === 'merge') {
                $totalFiles += $fileGroup->fileJobs->count();
            }

            foreach ($fileGroup->fileJobs as $fileJob) {
                // TODO: sometimes, because of a missing foreign key, input
                // stored files do not exist.
                if (! $fileJob->inputStoredFile) {
                    continue;
                }

                if ($fileJob->inputStoredFile->meta) {
                    $totalSize += $fileJob->inputStoredFile->meta->size;
                }
            }

            $amountFailed += $fileGroup->fileJobs->filter(function (FileJob $fileJob) {
                return $fileJob->has_error;
            })->count();
        }

        return FileJobStats::create([
            'date'          => $forDate,
            'tool_route'    => $toolRoute,
            'times_used'    => $timesUsed,
            'total_files'   => $totalFiles,
            'amount_failed' => $amountFailed,
            'total_size'    => $totalSize,
        ]);
    }
}
