<?php

namespace Tests\Unit\Support\Utils;

use App\Support\Utils\FileHash;
use Tests\TestCase;

class FileHashTest extends TestCase
{
    /** @test */
    function it_makes_a_unique_hash_from_a_file()
    {
        $fileHash = new FileHash();

        $a1 = $fileHash->make($this->testFilesStoragePath.'text/srt/empty.srt');

        $this->assertSame(40, strlen($a1));

        $b1 = $fileHash->make($this->testFilesStoragePath.'archives/zip/empty.zip');
        $b2 = $fileHash->make($this->testFilesStoragePath.'archives/zip/empty.zip');

        $this->assertNotSame($a1, $b1);
        $this->assertSame($b1, $b2);
    }

    /** @test */
    function hashes_are_made_from_file_content_not_from_file_name()
    {
        $fileHash = new FileHash();

        $a = $fileHash->make($this->testFilesStoragePath.'text/fake/dat.ass');
        $b = $fileHash->make($this->testFilesStoragePath.'text/fake/dat-copy.ass');

        $this->assertSame($a, $b);
    }
}
