<?php

namespace Tests\Unit\Subtitles\PlainText;

use App\Subtitles\PlainText\SrtCue;
use Tests\TestCase;

class SrtCueTest extends TestCase
{
    private function assert_Valid_TimingString($timingString)
    {
        $this->assertTrue(SrtCue::isTimingString($timingString), "'{$timingString}' is not a valid timing string");
    }

    private function assert_Invalid_TimingString($timingString)
    {
        $this->assertFalse(SrtCue::isTimingString($timingString), "'{$timingString}' is detected as a valid timing string, it should not be");
    }

    /** @test */
    function it_identifies_valid_timing_strings()
    {
        $this->assert_Valid_TimingString("00:00:00,000 --> 00:00:00,000");
        $this->assert_Valid_TimingString("99:59:59,999 --> 99:59:59,999\r\n");
        $this->assert_Valid_TimingString("00:00:28,400 --> 00:00:29,533 ");
        $this->assert_Valid_TimingString(" 00:13:25,707 --> 00:13:27,507");
    }

    /** @test */
    function it_identifies_valid_timing_strings_with_coordinates()
    {
        $this->assert_Valid_TimingString('00: 07: 39.053 --> 00: 07: 43.683  X1: 112 X2: 602 Y1: 444 Y2: 523');
        $this->assert_Valid_TimingString('00:02:26,407 --> 00:02:31,356 X1:100 X2:100 Y1:100 Y2:100');
        $this->assert_Valid_TimingString('00:02:26,407 --> 00:02:31,356  X1:100 X2:100 Y1:100 Y2:100');
        $this->assert_Valid_TimingString('00:00:36,452 --> 00:00:38,920  x1:205 x2:512 y1:450 y2:524');
    }

    /** @test */
    function it_identifies_timing_strings_with_common_mistakes()
    {
        // Dot instead of comma as milliseconds separator
        $this->assert_Valid_TimingString('00:00:00.000 --> 00:00:00.000');
        $this->assert_Valid_TimingString('00:00:00,000 --> 00:00:00.000');
        $this->assert_Valid_TimingString('00:00:05.123 --> 00:12:01,100');

        // Spaces after the colons. Google Translate causes this
        $this->assert_Valid_TimingString('00: 00: 05,123 --> 00: 12: 01,100');
        $this->assert_Valid_TimingString('00:00:05,123 --> 00:12: 01,100');

        // single dash in the arrow. Google translate causes this
        $this->assert_Valid_TimingString('00:00:00,400 -> 00:03:29,533');

        // one digit for hours, two for milliseconds
        $this->assert_Valid_TimingString('0:00:01,26 --> 0:00:03,36');
    }

    /** @test */
    function it_rejects_invalid_timing_strings()
    {
        $this->assertFalse(SrtCue::isTimingString("00:13:25,707 --> 00:13:27,5"));
        $this->assertFalse(SrtCue::isTimingString("100:00:01,266 --> 125:00:03,366"));

        // The most popular subtitle shifter on google can (incorrectly) turn timecodes negative
        $this->assertFalse(SrtCue::isTimingString("-1:59:40,266 --> -1:59:42,366"));
        $this->assertFalse(SrtCue::isTimingString("01:59:-0,266 --> 01:59:02,366"));
        $this->assertFalse(SrtCue::isTimingString("01:-9:40,266 --> 01:59:42,366"));
        $this->assertFalse(SrtCue::isTimingString("-1:59:57,100 --> 00:00:00,366"));

        $this->assertFalse(SrtCue::isTimingString("This man is out of ideas."));
        $this->assertFalse(SrtCue::isTimingString(""));
    }

    /** @test */
    function it_rejects_timing_strings_that_end_before_they_start()
    {
        $this->assertFalse(SrtCue::isTimingString("00:00:00,001 --> 00:00:00,000"));
    }

    /** @test */
    function it_makes_timing_strings()
    {
        $cue = new SrtCue();

        $cue->setTiming(0, 1000);

        $this->assertSame("00:00:00,000 --> 00:00:01,000", $cue->getTimingString());
    }

    /** @test */
    function it_preserves_valid_timing_strings()
    {
        $valuesShouldNotChange = [
            '00:00:00,000 --> 00:00:00,000',
            '00:01:01,266 --> 00:01:03,366',
            '12:34:56,789 --> 21:00:29,533',
        ];

        foreach($valuesShouldNotChange as $val) {
            $this->assertSame($val, (new SrtCue())->setTimingFromString($val)->getTimingString());
        }
    }

    /** @test */
    function it_correctly_handles_timing_strings_with_two_digits_for_the_milliseconds()
    {
        $cue = (new SrtCue)->setTimingFromString('00:00:01,26 --> 00:00:03,36');

        $this->assertSame('00:00:01,260 --> 00:00:03,360', $cue->getTimingString());

        $this->assertFalse(
            SrtCue::isTimingString($timing = '00:07:42,87 --> 00:07:42,796'),
            'Timing: '.$timing
        );
    }

    /** @test */
    function it_corrects_valid_timing_strings_with_common_mistakes()
    {
        // spaces after colons
        $cue = (new SrtCue())->setTimingFromString('00: 00: 05,123 --> 00: 12: 01,100');
        $this->assertSame('00:00:05,123 --> 00:12:01,100', $cue->getTimingString());

        // dot instead of comma
        $cue = (new SrtCue())->setTimingFromString('00:00:00.000 --> 00:00:00.000');
        $this->assertSame('00:00:00,000 --> 00:00:00,000', $cue->getTimingString());

        // single dash arrow
        $cue = (new SrtCue())->setTimingFromString('00:00:00,400 -> 00:03:29,533');
        $this->assertSame('00:00:00,400 --> 00:03:29,533', $cue->getTimingString());
    }

    /** @test */
    function it_does_not_preserve_coordinates()
    {
        $cue = (new SrtCue())->setTimingFromString('00:13:37,413 --> 00:13:41,167  X1:183 X2:533 Y1:444 Y2:523');

        $this->assertSame('00:13:37,413 --> 00:13:41,167', $cue->getTimingString());
    }

    /** @test */
    function timecodes_do_not_exceed_maximum_value()
    {
        $cue = new SrtCue();

        $cue->shift("9999999999999999999999999");

        $this->assertSame("99:59:59,999 --> 99:59:59,999", $cue->getTimingString());
    }

    /** @test */
    function timecodes_do_not_exceed_minimum_value()
    {
        $cue = new SrtCue();

        $cue->shift("-9999999999999999999999999");

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

    /** @test */
    function it_trims_timing_lines()
    {
        // regression test for a timing line that had a tab at the end.
        // ::isTimingString trimmed the line, but setTimingFromString didn't

        $cue = new SrtCue();

        $this->assertTrue(SrtCue::isTimingString("\t00:01:01,266 --> 00:01:03,366"));
        $this->assertTrue(SrtCue::isTimingString("00:01:01,266 --> 00:01:03,366\t"));

        $cue->setTimingFromString("\t00:01:01,266 --> 00:01:03,366");
        $cue->setTimingFromString("00:01:01,266 --> 00:01:03,366\t");
    }

    /** @test */
    function it_can_be_styled_to_show_on_top()
    {
        $cue = (new SrtCue)
            ->addLine('Wow!')
            ->stylePositionTop();

        $this->assertSame(['{\an8}Wow!'], $cue->getLines());

        // It should not apply the style twice.
        $cue = (new SrtCue)
            ->addLine('{\an8}Wow!')
            ->stylePositionTop();

        $this->assertSame(['{\an8}Wow!'], $cue->getLines());
    }
}
