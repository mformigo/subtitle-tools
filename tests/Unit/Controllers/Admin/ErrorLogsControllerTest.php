<?php

namespace Tests\Unit\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorLogsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_delete_the_error_log()
    {
        $errorLogPath = storage_path('logs/laravel.log');

        file_put_contents($errorLogPath, 'abc123');

        $this->assertFileExists($errorLogPath);

        $this->adminLogin()
            ->delete(route('admin.errorLog.delete'))
            ->assertStatus(302);

        $this->assertFileNotExists($errorLogPath);
    }
}
