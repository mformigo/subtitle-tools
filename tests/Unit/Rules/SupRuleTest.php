<?php

namespace Tests\Unit\Rules;

use App\Http\Rules\SupRule;
use Tests\TestCase;

class SupRuleTest extends TestCase
{
    /** @test */
    function it_passes_if_file_is_a_valid_sup()
    {
        $uploadedFile = $this->createUploadedFile('sup/three-english-cues.sup');

        $this->assertTrue(
            (new SupRule)->passes('', $uploadedFile)
        );
    }

    /** @test */
    function it_fails_if_file_is_not_a_valid_sup()
    {
        $uploadedFile = $this->createUploadedFile('text/ass/three-cues.ass');

        $this->assertFalse(
            (new SupRule)->passes('', $uploadedFile)
        );
    }
}
