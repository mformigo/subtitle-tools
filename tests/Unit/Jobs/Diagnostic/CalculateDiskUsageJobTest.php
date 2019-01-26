<?php

namespace Tests\Unit\Jobs\Diagnostic;

use App\Jobs\Diagnostic\CalculateDiskUsageJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalculateDiskUsageJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_writes_disk_usage_to_a_file()
    {
        $outputFilePath = storage_path('logs/disk-usage.txt');

        $this->calculateDiskUsage();

        $json = file_get_contents($outputFilePath);

        $this->assertSame(
            '{"size":"30gb","used":"11gb","available":"19gb","percentage":"36%","warning":false,"error":null}',
            $json
        );
    }

    /** @test */
    function it_creates_the_file_if_it_does_not_exist()
    {
        $outputFilePath = storage_path('logs/disk-usage.txt');

        if (file_exists($outputFilePath)) {
            unlink($outputFilePath);
        }

        $this->calculateDiskUsage();

        $this->assertFileExists($outputFilePath);
    }

    /** @test */
    function it_overwrites_the_existing_file()
    {
        $outputFilePath = storage_path('logs/disk-usage.txt');

        file_put_contents($outputFilePath, 'abc123');

        $this->calculateDiskUsage();

        $json = file_get_contents($outputFilePath);

        $this->assertSame(
            '{"size":"30gb","used":"11gb","available":"19gb","percentage":"36%","warning":false,"error":null}',
            $json
        );
    }

    private function calculateDiskUsage()
    {
        $job = new class extends CalculateDiskUsageJob {
            protected function executeCommand($diskName)
            {
                return implode("\n", [
                    'Filesystem      Size  Used Avail Use% Mounted on',
                    '/dev/vda1        30G   11G   19G  36% /',
                ]);
            }
        };

        $job->handle();
    }
}
