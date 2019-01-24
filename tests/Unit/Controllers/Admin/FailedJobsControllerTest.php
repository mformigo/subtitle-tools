<?php

namespace Tests\Unit\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FailedJobsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_truncate_the_failed_jobs_table()
    {
        DB::table('failed_jobs')->insert([
            'connection' => 'redis',
            'queue' => 'default',
            'payload' => '{}',
            'exception' => 'BOOL!',
            'failed_at' => now(),
        ]);

        $this->assertSame(1, DB::table('failed_jobs')->count());

        $this->adminLogin()
            ->delete(route('admin.failedJobs.truncate'))
            ->assertStatus(302);

        $this->assertSame(0, DB::table('failed_jobs')->count());
    }
}
