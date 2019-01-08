<?php

namespace Tests\Unit\Jobs;

use App\Jobs\GenerateSitemapJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateSitemapJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_generates_a_sitemap()
    {
        $path = public_path('sitemap.xml');

        if (file_exists($path)) {
            unlink($path);
        }

        (new GenerateSitemapJob)->handle();

        $this->assertFileExists($path);
    }
}
