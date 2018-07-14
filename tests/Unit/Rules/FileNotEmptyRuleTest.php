<?php

namespace Tests\Unit\Rules;

use App\Http\Rules\FileNotEmptyRule;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;

class FileNotEmptyRuleTest extends TestCase
{
    use CreatesUploadedFiles;

    /** @test */
    function it_passes_if_file_is_not_empty()
    {
        $uploadedFile = $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/three-cues.srt");

        $fileNotEmptyRule = new FileNotEmptyRule();

        $this->assertTrue($fileNotEmptyRule->passes('', $uploadedFile));
    }

    /** @test */
    function it_fails_if_file_is_empty()
    {
        $uploadedFile = $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/empty.srt");

        $fileNotEmptyRule = new FileNotEmptyRule();

        $this->assertFalse($fileNotEmptyRule->passes('', $uploadedFile));
    }
}
