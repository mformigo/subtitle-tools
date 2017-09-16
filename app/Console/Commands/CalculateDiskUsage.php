<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class CalculateDiskUsage extends Command
{
    protected $signature = 'st:calculate-disk-usage';

    protected $description = 'Calculates the disk usage and writes it to a file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $diskName = App::environment('local') ? '/dev/sda1' : '/dev/vda1';

        $outputFilePath = storage_path('logs/disk-usage--st.txt');

        $output = trim(
            shell_exec("df {$diskName} --human-readable 2>&1")
        );

        if(stripos($output, 'No such file or directory') !== false) {
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
    }
}
