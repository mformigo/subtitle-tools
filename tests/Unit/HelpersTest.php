<?php

namespace Tests\Unit;

use Tests\TestCase;


class HelpersTest extends TestCase
{
    function test_file_mime()
    {
        $this->assertSame("text/plain", file_mime(base_path("tests/Storage/TextEncodings/big5.txt")));
    }

}
