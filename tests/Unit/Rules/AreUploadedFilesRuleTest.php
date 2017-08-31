<?php

namespace Tests\Unit;

use App\Http\Rules\AreUploadedFilesRule;
use Tests\TestCase;

class AreUploadedFilesRuleTest extends TestCase
{
    // We have to mock a request to test this properly
    // So instead, this is tested in the Feature (controller) tests

    /** @test */
    function it_fails_if_all_files_are_not_valid_uploaded_files()
    {
        $areUploadedFilesRule = new AreUploadedFilesRule();

        $this->assertFalse($areUploadedFilesRule->passes('test', []));
    }
}
