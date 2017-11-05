<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GenerateSitemap::class,
        \App\Console\Commands\CollectMeta::class,
        \App\Console\Commands\CleanTemporaryStuff::class,
        \App\Console\Commands\CalculateDiskUsage::class,
        \App\Console\Commands\CleanDisk::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sitemap:generate')->dailyAt('2:00');

        $schedule->command('st:clean-temporary-stuff')->dailyAt('2:30');

        $schedule->command('st:collect-meta')->everyTenMinutes();

        $schedule->command('st:calculate-disk-usage')->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
