<?php

namespace Tests\Unit;

use Tests\TestCase;

class FileHashTest extends TestCase
{
    protected $reasonablyLongLength = 16;

    /** @test */
    function it_makes_a_unique_hash_from_a_file()
    {
        $fileHasher = new \App\Utils\FileHash();

        $a1 = $fileHasher->make("{$this->testFilesStoragePath}TextFiles/empty.srt");
        $b1 = $fileHasher->make("{$this->testFilesStoragePath}SubIdxFiles/error-and-nl.sub");
        $b2 = $fileHasher->make("{$this->testFilesStoragePath}SubIdxFiles/error-and-nl.sub");

        $this->assertTrue($a1 !== $b1);
        $this->assertTrue($b1 === $b2);
    }

    /** @test */
    function hashes_are_reasonably_long()
    {
        // Hashes should be reasonably long because they are used internally to make ids

        $fileHasher = new \App\Utils\FileHash();

        $b = $fileHasher->make("{$this->testFilesStoragePath}SubIdxFiles/error-and-nl.sub");

        $this->assertTrue(strlen($b) > $this->reasonablyLongLength);
    }

    /** @test */
    function it_can_hash_empty_files()
    {
        $fileHasher = new \App\Utils\FileHash();

        $a = $fileHasher->make("{$this->testFilesStoragePath}TextFiles/empty.srt");

        $this->assertTrue(strlen($a) > $this->reasonablyLongLength);
    }

    /** @test */
    function hashes_are_made_from_file_content_not_from_file_name()
    {
        $fileHasher = new \App\Utils\FileHash();

        $a = $fileHasher->make("{$this->testFilesStoragePath}TextFiles/Fake/torrent.srt");
        $b = $fileHasher->make("{$this->testFilesStoragePath}TextFiles/Fake/torrent-srt-copy.dat");

        $this->assertTrue($a === $b);
    }
}
