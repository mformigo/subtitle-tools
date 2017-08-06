<?php

namespace Tests\Unit;

use Tests\TestCase;

class FileHashTest extends TestCase
{
    /** @test */
    function it_makes_a_unique_hash_from_a_file()
    {
        $fileHasher = new \App\Utils\FileHash();

        $a = $fileHasher->make($this->testFilesStoragePath . "TextFiles/empty.srt");
        $b1 = $fileHasher->make($this->testFilesStoragePath . "SubIdxFiles/error-and-nl.sub");
        $b2 = $fileHasher->make($this->testFilesStoragePath . "SubIdxFiles/error-and-nl.sub");

        $this->assertTrue($a !== $b1);
        $this->assertTrue($b1 === $b2);
    }

    /** @test */
    function hashes_are_reasonably_long()
    {
        // Hashes should be reasonably long because they are used internally to make ids

        $fileHasher = new \App\Utils\FileHash();

        $a = $fileHasher->make($this->testFilesStoragePath . "TextFiles/empty.srt");
        $b = $fileHasher->make($this->testFilesStoragePath . "SubIdxFiles/error-and-nl.sub");

        $this->assertTrue(strlen($a) > 7);
        $this->assertTrue(strlen($b) > 7);
    }

}
