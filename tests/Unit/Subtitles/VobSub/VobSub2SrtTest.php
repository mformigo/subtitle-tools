<?php

namespace Tests\Unit\Subtitles\VobSub;


use App\Support\Facades\VobSub2Srt;
use Tests\TestCase;

class VobSub2SrtTest extends TestCase
{
    /** @test */
    function it_reads_languages_from_sub_files()
    {
        $languages = VobSub2Srt::get()
            ->path($this->testFilesStoragePath.'sub-idx/error-and-nl')
            ->languages();

        $this->assertSame(2, count($languages));

        $this->assertSame('0', $languages[0]['index']);
        $this->assertSame('1', $languages[1]['index']);

        // The IdxFile class provides the language, that is tested elsewhere.
        // For this test it is only important that the language isn't empty.
        $this->assertNotEmpty($languages[1]['language']);
        $this->assertNotEmpty($languages[0]['language']);
    }
}
