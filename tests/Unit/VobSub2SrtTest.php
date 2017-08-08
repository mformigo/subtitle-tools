<?php

namespace Tests\Unit;

use App\Subtitles\VobSub\VobSub2Srt;
use Tests\TestCase;

class VobSub2SrtTest extends TestCase
{
    /** @test */
    function it_reads_languages_from_sub_files()
    {
        $vobsub2srt = new VobSub2Srt($this->testFilesStoragePath . 'SubIdxFiles/error-and-nl');

        $languages = $vobsub2srt->getLanguages();

        $this->assertSame(2, count($languages));

        $this->assertSame("0", $languages[0]['index']);
        $this->assertSame("1", $languages[1]['index']);

        // The IdxFile class provides the language, that is tested elsewhere.
        // For this test it is only important that the language isn't empty.
        $this->assertFalse(empty($languages[1]['language']));
        $this->assertFalse(empty($languages[0]['language']));
    }

}
