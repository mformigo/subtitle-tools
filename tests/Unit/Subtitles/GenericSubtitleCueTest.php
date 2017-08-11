<?php

namespace Tests\Unit;

use Tests\TestCase;

class GenericSubtitleCueTest extends TestCase
{
    /** @test */
    function cues_are_buildable()
    {
        $cue = new \App\Subtitles\PlainText\GenericSubtitleCue();

        $cue->setTiming(0, 100)
            ->addLine('First line')
            ->addLine('Second line');

        $this->assertSame(['First line', 'Second line'], $cue->getLines());

        $this->assertSame(0,   $cue->getStartMs());
        $this->assertSame(100, $cue->getEndMs());
    }

    /** @test */
    function cue_lines_can_be_set_from_an_array()
    {
        $cue = new \App\Subtitles\PlainText\GenericSubtitleCue();
        $this->assertSame([], $cue->getLines());

        $cue->setLines(['First line', 'Second line']);
        $this->assertSame(['First line', 'Second line'], $cue->getLines());

        $cue->setLines(['Overwritten', 'Pretty cool']);
        $this->assertSame(['Overwritten', 'Pretty cool'], $cue->getLines());
    }

    /** @test */
    function it_trims_lines()
    {
        $cue = new \App\Subtitles\PlainText\GenericSubtitleCue();

        $cue->addLine("  First line  \r\n");

        $this->assertSame(['First line'], $cue->getLines());
    }

    /** @test */
    function it_does_not_add_empty_lines()
    {
        $cue = new \App\Subtitles\PlainText\GenericSubtitleCue();

        $cue->addLine("");
        $cue->addLine(" ");

        $this->assertSame([], $cue->getLines());
    }

    /** @test */
    function it_replaces_no_break_spaces()
    {
        $cue = new \App\Subtitles\PlainText\GenericSubtitleCue();

        $cue->addLine("Smi&nbsp;files&nbspare dumb");
        $cue->addLine("&nbsp;");
        $cue->addLine("&nbsp");

        $this->assertSame(['Smi files are dumb'], $cue->getLines());
    }

    /** @test */
    function start_ms_and_end_ms_can_be_the_same()
    {
        $cue = new \App\Subtitles\PlainText\GenericSubtitleCue();

        $cue->setTiming(100, 100);

        $this->assertSame(100, $cue->getStartMs());
        $this->assertSame(100, $cue->getEndMs());
    }

    /** @test */
    function end_ms_cant_be_earlier_than_start_ms()
    {
        $this->expectException(\Exception::class);

        $cue = new \App\Subtitles\PlainText\GenericSubtitleCue();

        $cue->setTiming(100, 50);
    }

    /** @test */
    function it_can_do_positive_shifts()
    {
        $cue = new \App\Subtitles\PlainText\GenericSubtitleCue();

        $cue->setTiming(10, 20)
            ->shift(100);

        $this->assertSame(110, $cue->getStartMs());
        $this->assertSame(120, $cue->getEndMs());
    }

    /** @test */
    function it_can_do_negative_shifts()
    {
        $cue = new \App\Subtitles\PlainText\GenericSubtitleCue();

        $cue->setTiming(10, 20)
            ->shift(-5);

        $this->assertSame(5, $cue->getStartMs());
        $this->assertSame(15, $cue->getEndMs());
    }

    /** @test */
    function timings_wont_go_below_zero()
    {
        $cue = new \App\Subtitles\PlainText\GenericSubtitleCue();

        $cue->setTiming(10, 20)
            ->shift(-15);

        $this->assertSame(0, $cue->getStartMs());
        $this->assertSame(5, $cue->getEndMs());

        $cue->shift(-100);

        $this->assertSame(0, $cue->getStartMs());
        $this->assertSame(0, $cue->getEndMs());

        $cue->setTiming(-2, -1);

        $this->assertSame(0, $cue->getStartMs());
        $this->assertSame(0, $cue->getEndMs());
    }

    /** @test */
    function it_can_alter_lines()
    {
        $cue = new \App\Subtitles\PlainText\GenericSubtitleCue();

        $cue->setLines(['Line #', 'Line #']);

        $cue->alterLines(function($line, $index) {
           return $line . $index;
        });

        $this->assertSame(['Line #0', 'Line #1'], $cue->getLines());

        $this->assertSame([0, 1], array_keys($cue->getLines()));
    }

    /** @test */
    function it_splits_altered_lines()
    {
        $cue = new \App\Subtitles\PlainText\GenericSubtitleCue();

        $cue->setLines(['Line 1', 'Line 2']);

        $cue->alterLines(function($line, $index) {
            return $line . "\n" . strtoupper($line);
        });

        $this->assertSame(['Line 1', 'LINE 1', 'Line 2', 'LINE 2'], $cue->getLines());

        $this->assertSame([0, 1, 2, 3], array_keys($cue->getLines()));
    }
}
