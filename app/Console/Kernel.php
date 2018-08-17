<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('st:randomize-url-keys')->dailyAt('1:55');
        $schedule->command('st:generate-sitemap'  )->dailyAt('2:00');

        // Janitor commands
        $schedule->command('st:prune-sub-idx-files'  )->dailyAt('2:05');
        $schedule->command('st:prune-temporary-files')->dailyAt('2:10');
        $schedule->command('st:prune-file-jobs'      )->dailyAt('2:15');
        $schedule->command('st:prune-sup-files'      )->dailyAt('2:20');
        $schedule->command('st:prune-stored-files'   )->dailyAt('2:50');
        $schedule->command('cache:clear'             )->dailyAt('2:55');

        // sort of fix a memory leak
        $schedule->command('queue:restart')->hourly();

        // Diagnostic commands
        $schedule->command('st:collect-meta'        )->everyFiveMinutes();
        $schedule->command('st:calculate-disk-usage')->everyTenMinutes();
        $schedule->command('st:collect-file-job-stats')->dailyAt('1:35');

        $schedule->command('backup:run-configless --only-db --disable-notifications --set-destination-disks=dropbox')->dailyAt('01:03');
    }

    protected function commands()
    {
        $this->load([
            __DIR__.'/Commands',
            __DIR__.'/Commands/Diagnostic',
            __DIR__.'/Commands/Janitor',
        ]);
    }
}
