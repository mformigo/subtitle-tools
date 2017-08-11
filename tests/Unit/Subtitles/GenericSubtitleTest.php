<?php

namespace Tests\Unit;

use App\Subtitles\PlainText\GenericSubtitle;
use App\Subtitles\PlainText\GenericSubtitleCue;
use Tests\TestCase;

class GenericSubtitleTest extends TestCase
{
    /** @test */
    function it_strips_angle_brackets()
    {
        $genericSubtitle = new GenericSubtitle();

        $genericCue = new GenericSubtitleCue();

        $genericCue->setLines([
            '<h1>wow</H1>',
            '<p class="KOREAN">',
        ]);

        $genericSubtitle->addCue($genericCue);

        $genericSubtitle->stripAngleBracketsFromCues();

        $this->assertSame(['wow'], $genericSubtitle->getCues()[0]->getLines());
    }

    /** @test */
    function it_strips_curly_brackets()
    {
        $genericSubtitle = new GenericSubtitle();

        $genericCue = new GenericSubtitleCue();

        $genericCue->setLines([
            '{h1}wow{}',
            '{123}',
        ]);

        $genericSubtitle->addCue($genericCue);

        $genericSubtitle->stripCurlyBracketsFromCues();

        $this->assertSame(['wow'], $genericSubtitle->getCues()[0]->getLines());
    }

    /** @test */
    function stripping_curly_brackets_also_removes_ass_drawings()
    {
        $genericSubtitle = new GenericSubtitle();

        $genericCue = new GenericSubtitleCue();

        $genericCue->setLines([
            '{h1}wow{}',
            '{\p0}1 0 1 0 2 0 1', // this cue contains \p0, which indicates that it is an ass drawing
            '{\p1}1 0 1 0 2 0 1', // this cue contains \p1, which indicates that it is an ass drawing
        ]);

        $genericSubtitle->addCue($genericCue);

        $genericSubtitle->stripCurlyBracketsFromCues();

        $this->assertSame(['wow'], $genericSubtitle->getCues()[0]->getLines());
    }
}
