<?php

namespace Tests\Unit;

use Tests\TestCase;

class HelpersTest extends TestCase
{
    /** @test */
    function file_mime_returns_the_file_mime()
    {
        $this->assertSame("text/plain", file_mime(base_path("tests/Storage/TextEncodings/big5.txt")));
    }

    /** @test */
    function storage_disk_file_path_returns_the_correct_path()
    {
        $this->assertTrue(ends_with(storage_disk_file_path('dirname'), '/storage/testing/dirname'));

        $this->assertTrue(ends_with(storage_disk_file_path('/dirname/file.jpg'), '/storage/testing/dirname/file.jpg'));
    }
}
