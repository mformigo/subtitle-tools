<?php

namespace Tests\Unit\Jobs\Diagnostic;

use App\Jobs\Diagnostic\CalculateDiskUsageJob;
use App\Models\DiskUsage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateDiskUsageJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_creates_a_disk_usage_model()
    {
        $this->calculateDiskUsage();

        $diskUsage = DiskUsage::findOrFail(1);

        $this->assertTrue($diskUsage->total_size > 0);
        $this->assertTrue($diskUsage->total_used > 0);

        $this->assertTrue($diskUsage->total_size > $diskUsage->total_used);
    }

    private function calculateDiskUsage()
    {
        $job = new class extends CalculateDiskUsageJob {
            protected function executeTotalCommand($diskName)
            {
                return implode("\n", [
                    'Filesystem     1K-blocks   Used Available Use% Mounted on',
                    '/dev/vda1        482922K 48300K   409688K  11% /boot',
                ]);
            }
        };

        $job->handle();
    }
}
