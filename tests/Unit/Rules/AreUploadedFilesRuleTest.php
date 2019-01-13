<?php

namespace Tests\Unit\Rules;

use App\Http\Rules\AreUploadedFilesRule;
use Tests\TestCase;

class AreUploadedFilesRuleTest extends TestCase
{
    /** @test */
    function it_fails_if_all_files_are_not_valid_uploaded_files()
    {
        $this->assertFalse(
            (new AreUploadedFilesRule)->passes('test', null)
        );

        $this->assertFalse(
            (new AreUploadedFilesRule)->passes('test', [])
        );

        $this->assertFalse(
            (new AreUploadedFilesRule)->passes('test', 'AAA')
        );
    }
}
