<?php

namespace App\Console;

use App\Jobs\Diagnostic\CalculateDiskUsageJob;
use App\Jobs\Diagnostic\CollectFileJobStatsJob;
use App\Jobs\Diagnostic\CollectMetaForStoredFilesJob;
use App\Jobs\Diagnostic\CollectMetaForSupJobsJob;
use App\Jobs\GenerateSitemapJob;
use App\Jobs\Janitor\PruneFileJobsJob;
use App\Jobs\Janitor\PruneStoredFilesJob;
use App\Jobs\Janitor\PruneSubIdxFilesJob;
use App\Jobs\Janitor\PruneSubIdxTableJob;
use App\Jobs\Janitor\PruneSupJobsJob;
use App\Jobs\Janitor\PruneSupJobsTableJob;
use App\Jobs\Janitor\PruneTemporaryFilesJob;
use App\Jobs\RandomizeSubIdxUrlKeysJob;
use App\Jobs\RandomizeSupUrlKeysJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(RandomizeSubIdxUrlKeysJob::class)->dailyAt('1:50');
        $schedule->job(RandomizeSupUrlKeysJob::class)->dailyAt('1:55');
        $schedule->job(GenerateSitemapJob::class)->dailyAt('2:00');

        // Janitor commands
        $schedule->job(PruneTemporaryFilesJob::class)->dailyAt('2:10');
        $schedule->job(PruneFileJobsJob::class)->dailyAt('2:15');
        $schedule->job(PruneSupJobsJob::class)->dailyAt('2:20');
        $schedule->job(PruneSubIdxTableJob::class)->dailyAt('2:25');
        $schedule->job(PruneSubIdxFilesJob::class)->dailyAt('2:26');
        $schedule->job(PruneSupJobsTableJob::class)->dailyAt('2:30');
        $schedule->job(PruneStoredFilesJob::class)->dailyAt('2:50');
        $schedule->command('cache:clear')->dailyAt('2:55');

        // sort of fix a memory leak
        $schedule->command('queue:restart')->hourly();

        // Diagnostic
        $schedule->job(CollectMetaForStoredFilesJob::class)->everyFifteenMinutes();
        $schedule->job(CollectMetaForSupJobsJob::class)->everyFifteenMinutes();
        $schedule->job(CalculateDiskUsageJob::class)->cron('0 */3 * * *');
        $schedule->job(CollectFileJobStatsJob::class)->twiceDaily(2, 14);

        $schedule->command('backup:run-configless --only-db --disable-notifications --set-destination-disks=dropbox')->weeklyOn(1, '01:03');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
