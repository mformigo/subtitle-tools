<?php

namespace Tests\Unit;

use App\Http\Rules\SupRule;
use Tests\CreatesUploadedFiles;
use Tests\TestCase;

class SupRuleTest extends TestCase
{
    use CreatesUploadedFiles;

    /** @test */
    function it_passes_if_file_is_a_valid_sup()
    {
        $uploadedFile = $this->createUploadedFile($this->testFilesStoragePath.'Sup/three-english-cues.sup');

        $supRule = new SupRule();

        $this->assertTrue($supRule->passes('', $uploadedFile));
    }

    /** @test */
    function it_fails_if_file_is_not_a_valid_sup()
    {
        $uploadedFile = $this->createUploadedFile($this->testFilesStoragePath.'TextFiles/three-cues.ass');

        $supRule = new SupRule();

        $this->assertFalse($supRule->passes('', $uploadedFile));
    }
}
