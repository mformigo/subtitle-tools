<?php

namespace App\Jobs\Diagnostic;

use App\Jobs\BaseJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class CalculateDiskUsageJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
        $diskName = app()->environment('local') ? '/dev/sda1' : '/dev/vda1';

        $output = $this->executeCommand($diskName);

        file_put_contents(
            storage_path('logs/disk-usage.txt'),
            $this->parseOutput($output, $diskName)
        );
    }

    protected function executeCommand($diskName)
    {
        return trim(
            shell_exec("df $diskName --human-readable 2>&1")
        );
    }

    private function parseOutput($output, $diskName): string
    {
        if (stripos($output, 'No such file or directory') !== false) {
            return json_encode(['warning' => true, 'error' => $output]);
        }

        // Filesystem      Size  Used Avail Use% Mounted on
        // /dev/vda1        30G   11G   19G  36% /

        $output = str_ireplace('g', 'gb', $output);

        $output = trim(
            str_after($output, $diskName)
        );

        [$size, $used, $available, $percentage] = preg_split('/ +/', $output);

        return json_encode([
            'size' => $size,
            'used' => $used,
            'available' => $available,
            'percentage' => $percentage,
            'warning' => trim($percentage, '%') > 60,
            'error' => null,
        ]);
    }
}
