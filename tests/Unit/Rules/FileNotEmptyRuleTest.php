<?php

namespace Tests\Unit\Rules;

use App\Http\Rules\FileNotEmptyRule;
use Tests\TestCase;

class FileNotEmptyRuleTest extends TestCase
{
    /** @test */
    function it_passes_if_file_is_not_empty()
    {
        $uploadedFile = $this->createUploadedFile('text/srt/three-cues.srt');

        $this->assertTrue(
            (new FileNotEmptyRule)->passes('', $uploadedFile)
        );
    }

    /** @test */
    function it_fails_if_file_is_empty()
    {
        $uploadedFile = $this->createUploadedFile('text/srt/empty.srt');

        $this->assertFalse(
            (new FileNotEmptyRule)->passes('', $uploadedFile)
        );
    }
}
