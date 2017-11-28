<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('st:generate-sitemap')->dailyAt('2:00');

        // Janitor commands
        $schedule->command('st:prune-sub-idx-files'  )->dailyAt('2:05');
        $schedule->command('st:prune-temporary-files')->dailyAt('2:15');
        $schedule->command('st:prune-file-jobs'      )->dailyAt('2:25');
        $schedule->command('st:prune-stored-files'   )->dailyAt('2:35');
        $schedule->command('cache:clear'             )->dailyAt('2:45');

        // sort of fix a memory leak
        $schedule->command('queue:restart')->hourly();

        // Diagnostic commands
        $schedule->command('st:collect-meta'        )->everyTenMinutes();
        $schedule->command('st:calculate-disk-usage')->everyTenMinutes();
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
