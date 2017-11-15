<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('st:generate-sitemap')->dailyAt('2:00');

        $schedule->command('st:prune-sub-idx-files')->dailyAt('2:10');

        $schedule->command('st:clean-temporary-stuff')->dailyAt('2:20');

        $schedule->command('st:delete-orphaned-files')->dailyAt('2:30');

        $schedule->command('cache:clear')->dailyAt('2:40');

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
