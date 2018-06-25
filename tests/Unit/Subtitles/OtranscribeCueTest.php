<?php

namespace Tests\Unit;

use App\Subtitles\PlainText\GenericSubtitleCue;

use App\Subtitles\PlainText\Otranscribe\OtranscribeCue;
use Tests\TestCase;

class OtranscribeCueTest extends TestCase
{
    /** @test */
    function it_identifies_valid_timing_strings()
    {
        $this->isValidTimingString('00:03 wow');
        $this->isValidTimingString('00:00 Text!');
        $this->isValidTimingString('01:00');
        $this->isValidTimingString('99:99 ');
    }

    /** @test */
    function it_identifies_timing_strings_that_use_a_no_break_space()
    {
        $nbsp = html_entity_decode('&nbsp;');

        $this->isValidTimingString('00:03'.$nbsp.'wow');
    }

    /** @test */
    function it_rejects_invalid_timing_strings()
    {
        $this->isInvalidTimingString('');
        $this->isInvalidTimingString('01 text');
        $this->isInvalidTimingString('01:0 text');
        $this->isInvalidTimingString('1:00 text');
        $this->isInvalidTimingString('Wow crazy 00:00');
    }

    /** @test */
    function it_constructs()
    {
        $nbsp = html_entity_decode('&nbsp;');

        $cue = new OtranscribeCue('01:05'.$nbsp.'Crazy'.$nbsp.'stuff');

        $this->assertSame(65000, $cue->getStartMs());
        $this->assertSame(65000, $cue->getEndMs());

        $this->assertSame(['Crazy stuff'], $cue->getLines());
    }

    /** @test */
    function it_constructs_when_there_is_no_text()
    {
        $cue = new OtranscribeCue('01:05');

        $this->assertSame(65000, $cue->getStartMs());
        $this->assertSame(65000, $cue->getEndMs());

        $this->assertSame([], $cue->getLines());
    }

    /** @test */
    function it_transforms_to_generic_cue()
    {
        $mpl2Cue = new OtranscribeCue('10:30 The text');

        $genericCue = $mpl2Cue->toGenericCue();

        $this->assertTrue($genericCue instanceof GenericSubtitleCue);

        $this->assertFalse($genericCue instanceof OtranscribeCue);

        $this->assertSame(630000, $genericCue->getStartMs());
        $this->assertSame(630000, $genericCue->getEndMs());

        $this->assertSame(['The text'], $genericCue->getLines());
    }

    private function isValidTimingString($string)
    {
        $this->assertTrue(
            OtranscribeCue::isTimingString($string)
        );
    }

    private function isInvalidTimingString($string)
    {
        $this->assertFalse(
            OtranscribeCue::isTimingString($string)
        );
    }
}
