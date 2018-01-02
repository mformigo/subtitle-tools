<?php

namespace App\Console\Commands\Diagnostic;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CalculateDiskUsage extends Command
{
    protected $signature = 'st:calculate-disk-usage';

    protected $description = 'Calculate current disk usage for displaying in the admin dashboard';

    public function handle()
    {
        $this->info('Calculating disk usage...');

        $diskName = app()->environment('local') ? '/dev/sda1' : '/dev/vda1';

        $outputFilePath = storage_disk_file_path('diagnostic/disk-usage.txt');

        if (! Storage::exists('diagnostic/')) {
            Storage::makeDirectory('diagnostic/');
        }

        $output = trim(
            shell_exec("df {$diskName} --human-readable 2>&1")
        );

        if (stripos($output, 'No such file or directory') !== false) {
            file_put_contents($outputFilePath, $output);
        }
        else {
            // Filesystem      Size  Used Avail Use% Mounted on
            // /dev/vda1        30G   11G   19G  36% /

            $output = str_ireplace('g', 'gb', $output);

            list($size, $used, $available, $usedPercentage) = preg_split('/ +/',
                trim(
                    str_after($output, $diskName)
                )
            );

            file_put_contents($outputFilePath, "{$used} / {$size} ({$usedPercentage})");
        }

        $this->comment('Done!');
    }
}
