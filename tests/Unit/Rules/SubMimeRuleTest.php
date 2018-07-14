<?php

namespace Tests\Unit\Rules;

use App\Http\Rules\SubMimeRule;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;

class SubMimeRuleTest extends TestCase
{
    use CreatesUploadedFiles;

    /** @test */
    function it_passes_if_file_has_valid_sub_mime()
    {
        $uploadedFile = $this->createUploadedFile('sub-idx/error-and-nl.sub');

        $this->assertTrue(
            (new SubMimeRule)->passes('', $uploadedFile)
        );
    }

    /** @test */
    function it_fails_if_file_does_not_have_a_valid_sub_mime()
    {
        $uploadedFile = $this->createUploadedFile('text/srt/three-cues.srt');

        $this->assertFalse(
            (new SubMimeRule)->passes('', $uploadedFile)
        );
    }
}
