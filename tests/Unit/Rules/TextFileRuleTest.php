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
        $uploadedFile = $this->createUploadedFile('text/srt/three-cues.srt');

        $this->assertTrue(
            (new TextFileRule)->passes('', $uploadedFile)
        );
    }

    /** @test */
    function it_fails_if_file_is_not_a_text_file()
    {
        $uploadedFile = $this->createUploadedFile('text/fake/dat.ass');

        $this->assertFalse(
            (new TextFileRule)->passes('', $uploadedFile)
        );
    }
}
