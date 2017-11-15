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
        $schedule->command('st:prune-sub-idx-files')->dailyAt('2:17');
        $schedule->command('st:prune-temporary-files')->dailyAt('2:27');
        $schedule->command('st:prune-stored-files')->dailyAt('2:37');
        $schedule->command('cache:clear')->dailyAt('2:47');

        // Diagnostic commands
        $schedule->command('st:collect-meta')->everyTenMinutes();
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
