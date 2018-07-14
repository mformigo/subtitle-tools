<?php

namespace Tests\Unit\Rules;

use App\Http\Rules\AreUploadedFilesRule;
use Tests\TestCase;

class AreUploadedFilesRuleTest extends TestCase
{
    // We have to mock a request to test this properly
    // So instead, this is tested in the controller tests

    /** @test */
    function it_fails_if_all_files_are_not_valid_uploaded_files()
    {
        $areUploadedFilesRule = new AreUploadedFilesRule();

        $this->assertFalse($areUploadedFilesRule->passes('test', []));
    }
}
