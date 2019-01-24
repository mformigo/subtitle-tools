<?php

namespace Tests\Unit\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_delete_the_feedback_log()
    {
        $feedbackFilePath = storage_path('logs/feedback.log');

        file_put_contents($feedbackFilePath, 'abc123');

        $this->assertFileExists($feedbackFilePath);

        $this->adminLogin()
            ->delete(route('admin.feedback.delete'))
            ->assertStatus(302);

        $this->assertFileNotExists($feedbackFilePath);
    }
}
