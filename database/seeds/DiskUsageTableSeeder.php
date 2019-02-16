<?php

use App\Jobs\Diagnostic\CalculateDiskUsageJob;
use App\Models\DiskUsage;
use Illuminate\Database\Seeder;

class DiskUsageTableSeeder extends Seeder
{
    public function run()
    {
        factory(DiskUsage::class)->create(['created_at' => now()->subHours(5)]);
        factory(DiskUsage::class)->create(['created_at' => now()->subHours(4)]);
        factory(DiskUsage::class)->create(['created_at' => now()->subHours(3)]);
        factory(DiskUsage::class)->create(['created_at' => now()->subHours(2)]);
        factory(DiskUsage::class)->create(['created_at' => now()->subHours(1)]);

        (new CalculateDiskUsageJob)->handle();
    }
}
