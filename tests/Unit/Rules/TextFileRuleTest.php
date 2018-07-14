<?php

namespace Tests\Unit\Rules;

use App\Http\Rules\TextFileRule;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;

class TextFileRuleTest extends TestCase
{
    use CreatesUploadedFiles;

    /** @test */
    function it_passes_if_file_is_a_text_file()
    {
        $uploadedFile = $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/three-cues.srt");

        $textFileRule = new TextFileRule();

        $this->assertTrue($textFileRule->passes('', $uploadedFile));
    }

    /** @test */
    function it_fails_if_file_is_not_a_text_file()
    {
        $uploadedFile = $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/Fake/dat.ass");

        $textFileRule = new TextFileRule();

        $this->assertFalse($textFileRule->passes('', $uploadedFile));
    }
}
