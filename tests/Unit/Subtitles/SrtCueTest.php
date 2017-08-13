<?php

namespace Tests\Unit;

use App\Subtitles\PlainText\SrtCue;
use Tests\TestCase;

class SrtCueTest extends TestCase
{
    /** @test */
    function it_identifies_valid_timing_strings()
    {
        $this->assertTrue(SrtCue::isTimingString("00:00:00,000 --> 00:00:00,000"));
        $this->assertTrue(SrtCue::isTimingString("99:59:59,999 --> 99:59:59,999\r\n"));
        $this->assertTrue(SrtCue::isTimingString("00:00:28,400 --> 00:00:29,533 "));
        $this->assertTrue(SrtCue::isTimingString(" 00:13:25,707 --> 00:13:27,507"));
    }

    /** @test */
    function it_rejects_invalid_timing_strings()
    {
        $this->assertFalse(SrtCue::isTimingString("00:00:00,001 --> 00:00:00,000"));
        $this->assertFalse(SrtCue::isTimingString("00:00:00,400 -> 00:03:29,533"));
        $this->assertFalse(SrtCue::isTimingString("00:13:25,707 --> 00:13:27,5"));
        $this->assertFalse(SrtCue::isTimingString("0:00:01,266 --> 0:00:03,366"));
        $this->assertFalse(SrtCue::isTimingString("100:00:01,266 --> 125:00:03,366"));

        // The most popular subtitle shifter on google can (incorrectly) turn timecodes negative
        $this->assertFalse(SrtCue::isTimingString("-1:59:40,266 --> -1:59:42,366"));
        $this->assertFalse(SrtCue::isTimingString("01:59:-0,266 --> 01:59:02,366"));
        $this->assertFalse(SrtCue::isTimingString("01:-9:40,266 --> 01:59:42,366"));
        $this->assertFalse(SrtCue::isTimingString("-1:59:57,100 --> 00:00:00,366"));

        $this->assertFalse(SrtCue::isTimingString("This man is out of ideas."));
        $this->assertFalse(SrtCue::isTimingString(""));
        $this->assertFalse(SrtCue::isTimingString(null));
        $this->assertFalse(SrtCue::isTimingString(false));
    }

    /** @test */
    function it_rejects_timing_strings_that_end_before_they_start()
    {
        $this->assertFalse(SrtCue::isTimingString("00:00:00,001 --> 00:00:00,000"));
    }

    /** @test */
    function it_rejects_timing_strings_with_a_location_indicator()
    {
        // Timing lines can have a location indicator, but we don't support that
        $this->assertFalse(SrtCue::isTimingString("00:02:26,407 --> 00:02:31,356 X1:100 X2:100 Y1:100 Y2:100"));
    }

    /** @test */
    function it_makes_timing_strings()
    {
        $cue = new SrtCue();

        $cue->setTiming(0, 1000);

        $this->assertSame("00:00:00,000 --> 00:00:01,000", $cue->getTimingString());
    }

    /** @test */
    function it_preserves_timing_strings()
    {
        $valuesShouldNotChange = [
            "00:00:00,000 --> 00:00:00,000",
            "00:01:01,266 --> 00:01:03,366",
            "12:34:56,789 --> 21:00:29,533",
        ];

        foreach($valuesShouldNotChange as $val) {
            $this->assertSame($val, (new SrtCue())->setTimingFromString($val)->getTimingString());
        }
    }

    /** @test */
    function timecodes_do_not_exceed_maximum_value()
    {
        $cue = new SrtCue();

        $cue->shift(9999999999999999999999999);

        $this->assertSame("99:59:59,999 --> 99:59:59,999", $cue->getTimingString());
    }

    /** @test */
    function timecodes_do_not_exceed_minimum_value()
    {
        $cue = new SrtCue();

        $cue->shift(-9999999999999999999999999);

        $this->assertSame("00:00:00,000 --> 00:00:00,000", $cue->getTimingString());
    }

    /** @test */
    function it_converts_to_array()
    {
        $cue = new SrtCue();

        $cue->setTimingFromString('00:01:01,266 --> 00:01:03,366')
            ->addLine('First line')
            ->addLine('Second line!');

        $this->assertSame([
            '00:01:01,266 --> 00:01:03,366',
            'First line',
            'Second line!',
            '',
        ], $cue->toArray());
    }
}
