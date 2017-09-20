<?php

namespace Tests\Feature;

use Tests\TestCase;

class NotFoundTest extends TestCase
{
    private function putOnBlacklist($requestPath)
    {
        foreach(array_wrap($requestPath) as $paths) {
            file_put_contents(
                storage_disk_file_path('diagnostic/404-blacklist.txt'),
                $paths . "\r\n",
                FILE_APPEND
            );
        }

        return $this;
    }

    private function assert404LogHas($requestPath)
    {
        $logFilePath = storage_disk_file_path('diagnostic/404.txt');

        if(!file_exists($logFilePath)) {
            $this->fail('404 log was not created');
        }

        $logContent = file_get_contents($logFilePath);

        $this->assertTrue(str_contains($logContent, "|{$requestPath}\r\n"));
    }

    private function assert404LogNotHas($requestPath)
    {
        $logFilePath = storage_disk_file_path('diagnostic/404.txt');

        if(!file_exists($logFilePath)) {
            $this->assertTrue(true);
            return;
        }

        $logContent = file_get_contents($logFilePath);

        $this->assertFalse(str_contains($logContent, "|{$requestPath}\r\n"));
    }

    /** @test */
    function it_logs_404_requests()
    {
        $this->get('/does-not-exist')
             ->assertStatus(404);

        $this->assert404LogHas('/does-not-exist');
    }

    /** @test */
    function it_does_not_log_blacklisted_404_requests()
    {
        $this->putOnBlacklist([
            '/first-entry',
            '/does-not-exist',
        ]);

        $this->get('/does-not-exist')
             ->assertStatus(404);

        $this->assert404LogNotHas('/does-not-exist');
    }

    /** @test */
    function it_does_strict_matching_on_blacklist_paths()
    {
        $this->putOnBlacklist('/does-not-exist');

        $this->get('/does-not-exist/deeper')
            ->assertStatus(404);

        $this->assert404LogHas('/does-not-exist/deeper');
    }
}
