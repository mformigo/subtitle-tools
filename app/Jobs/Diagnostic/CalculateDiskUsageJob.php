<?php

namespace App\Jobs\Diagnostic;

use App\Jobs\BaseJob;
use App\Models\DiskUsage;
use Illuminate\Contracts\Queue\ShouldQueue;

class CalculateDiskUsageJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
        $diskName = app()->environment('local') ? '/dev/sda1' : '/dev/vda1';

        $output = $this->executeTotalCommand($diskName);

        [$totalSize, $totalUsed] = $this->parseTotalUsage($output, $diskName);

        $basePath = storage_path('app/');

        DiskUsage::create([
            'total_size' => $totalSize,
            'total_used' => $totalUsed,
            'stored_files_dir_size' => $this->directorySize($basePath.'stored-files'),
            'sub_idx_dir_size' => $this->directorySize($basePath.'sub-idx'),
            'temp_dirs_dir_size' => $this->directorySize($basePath.'temporary-dirs'),
            'temp_files_dir_size' => $this->directorySize($basePath.'temporary-files'),
        ]);
    }

    protected function executeTotalCommand($diskName)
    {
        return trim(
            shell_exec("df $diskName --block-size=K 2>&1")
        );
    }

    private function parseTotalUsage($output, $diskName)
    {
        //    Filesystem     1K-blocks   Used Available Use% Mounted on
        //    /dev/sda1        482922K 48300K   409688K  11% /boot

        $output = trim(
            str_after($output, $diskName)
        );

        [$size, $used] = preg_split('/ +/', $output);

        return [trim($size, 'K'), trim($used, 'K')];
    }

    protected function directorySize($directoryPath)
    {
        if (! file_exists($directoryPath) || ! is_dir($directoryPath)) {
            throw new \RuntimeException('Directory does not exist: '.$directoryPath);
        }

        $output = trim(
            shell_exec("du $directoryPath -ks 2>&1")
        );

        return (int) str_before($output, ' ');
    }
}
