<?php

namespace Tests\Unit\Support;

use Tests\TestCase;

class HelpersTest extends TestCase
{
    /** @test */
    function file_mime_returns_the_file_mime()
    {
        $this->assertSame(
            'text/plain',
            file_mime($this->testFilesStoragePath.'sub-idx/error-and-nl.idx')
        );
    }

    /** @test */
    function file_mime_handles_broken_files()
    {
        $this->assertSame(
            'application/octet-stream',
            file_mime($this->testFilesStoragePath.'other/file-with-broken-mime.dat')
        );
    }

    /** @test */
    function storage_disk_file_path_returns_the_correct_path()
    {
        $this->assertStringEndsWith(
            '/storage/testing/dirname',
            storage_disk_file_path('dirname')
        );

        $this->assertStringEndsWith(
            '/storage/testing/dirname/file.jpg',
            storage_disk_file_path('/dirname/file.jpg')
        );
    }
}
