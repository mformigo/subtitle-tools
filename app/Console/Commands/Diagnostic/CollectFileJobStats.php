<?php

namespace App\Console\Commands\Diagnostic;

use App\Models\Diagnostic\FileJobStats;
use App\Models\FileGroup;
use Illuminate\Console\Command;

class CollectFileJobStats extends Command
{
    protected $signature = 'st:collect-file-job-stats';

    protected $description = 'Collect file job stats for yesterday';

    public function handle()
    {
        // Add an asterisk that is used to collect stats for all tools at once.
        $toolRoutes = array_merge(['*'], config('st.tool_routes'));

        foreach ($toolRoutes as $toolRoute) {
            $this->comment('Collecting stats for <info>'.$toolRoute.'</info>');

            $this->collectStats($toolRoute);
        }
    }

    protected function collectStats($toolRoute)
    {
        $yesterday = now()->subDay(1)->format('Y-m-d');

        $fileGroups = FileGroup::query()
            ->when($toolRoute !== '*', function ($query) use ($toolRoute) {
                $query->where('tool_route', $toolRoute);
            })
            ->whereDate('created_at', $yesterday)
            ->with('fileJobs')
            ->with('fileJobs.inputStoredFile')
            ->with('fileJobs.inputStoredFile.meta')
            ->get();

        $timesUsed = count($fileGroups);
        $totalFiles = 0;
        $totalSize = 0;

        foreach ($fileGroups as $fileGroup) {
            $totalFiles += $fileGroup->fileJobs->count();

            foreach ($fileGroup->fileJobs as $fileJob) {
                if ($fileJob->inputStoredFile->meta) {
                    $totalSize += $fileJob->inputStoredFile->meta->size;
                }
            }
        }

        FileJobStats::create([
            'date'        => $yesterday,
            'tool_route'  => $toolRoute,
            'times_used'  => $timesUsed,
            'total_files' => $totalFiles,
            'total_size'  => $totalSize,
        ]);
    }
}
