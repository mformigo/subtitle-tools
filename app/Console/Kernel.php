<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GenerateSitemap::class,
        \App\Console\Commands\QueueCollectStoredFileMetaJobs::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sitemap:generate')->dailyAt('2:00');

        $schedule->command('st:collect-stored-file-meta')->everyTenMinutes();
    }

    /**
     * Register the Closure based commands for the application.
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
