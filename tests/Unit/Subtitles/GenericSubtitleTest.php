<?php

namespace Tests\Unit;

use App\Subtitles\ContainsGenericCues;
use App\Subtitles\PlainText\GenericSubtitle;
use App\Subtitles\PlainText\GenericSubtitleCue;
use Tests\TestCase;

class GenericSubtitleTest extends TestCase
{
    protected function makeSingleCueGenericSubtitle($lines)
    {
        return (new GenericSubtitle)->addCue(
            (new GenericSubtitleCue)->setLines($lines)
        );
    }

    /** @test */
    function it_strips_angle_brackets()
    {
        $genericSub = $this->makeSingleCueGenericSubtitle([
            '<h1>wow</H1>',
            '<p class="KOREAN">',
            '<font color="#ffffff">And on top of all that, you\'re</font>',
            '<font color="#ffffff">capable of more complex thought,</font>',
        ]);

        $this->assertTrue($genericSub instanceof ContainsGenericCues);

        $this->assertSame(
            ['wow', 'And on top of all that, you\'re', 'capable of more complex thought,'],
            $genericSub->stripAngleBracketsFromCues()->getCues()[0]->getLines()
        );
    }

    /** @test */
    function it_strips_angle_brackets_across_lines()
    {
        $genericSub = $this->makeSingleCueGenericSubtitle([
            '<wow',
            'yo>test',
        ]);

        $this->assertSame(
            ['test'],
            $genericSub->stripAngleBracketsFromCues()->getCues()[0]->getLines()
        );
    }

    /** @test */
    function it_strips_curly_brackets()
    {
        $genericSub = $this->makeSingleCueGenericSubtitle([
            '{h1}wow{}',
            '{123}',
        ]);

        $this->assertSame(
            ['wow'],
            $genericSub->stripCurlyBracketsFromCues()->getCues()[0]->getLines()
        );
    }

    /** @test */
    function it_strips_curly_brackets_across_lines()
    {
        $genericSub = $this->makeSingleCueGenericSubtitle([
            '{ man singing in Spanish',
            'echoing } wow!',
        ]);

        $this->assertSame(
            ['wow!'],
            $genericSub->stripCurlyBracketsFromCues()->getCues()[0]->getLines()
        );
    }

    /** @test */
    function it_strips_parentheses()
    {
        $genericSub = $this->makeSingleCueGenericSubtitle([
            'No!',
            '( screaming )',
        ]);

        $this->assertSame(
            ['No!'],
            $genericSub->stripParenthesesFromCues()->getCues()[0]->getLines()
        );
    }

    /** @test */
    function it_strips_parentheses_and_leftover_dashes()
    {
        $genericSub = $this->makeSingleCueGenericSubtitle([
            '- ( clattering continues )',
            '- ( floorboards creaking )',
        ]);

        $this->assertFalse(
            $genericSub->stripParenthesesFromCues()->hasCues(),
            'Generic subtitle still has cues, it should have removed everything, including the left-over dashes'
        );
    }

    /** @test */
    function it_strips_parentheses_across_lines()
    {
        $genericSub = $this->makeSingleCueGenericSubtitle([
            '( man singing in Spanish',
            'echoing )',
        ]);

        $this->assertFalse(
            $genericSub->stripParenthesesFromCues()->hasCues(),
            'Generic subtitle still has cues, it should have removed everything'
        );
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

        $this->assertSame(
            ['wow'], 
            $genericSubtitle->getCues()[0]->getLines()
        );
    }

    /** @test */
    function it_removes_duplicate_cues()
    {
        $genericSubtitle = new GenericSubtitle();

        $genericSubtitle->addCue((new GenericSubtitleCue())->setLines(['Summer of George'])->setTiming(100, 1200));
        $genericSubtitle->addCue((new GenericSubtitleCue())->setLines(['Summer of George'])->setTiming(100, 600));
        $genericSubtitle->addCue((new GenericSubtitleCue())->setLines(['Summer of George'])->setTiming(100, 1200));
        $genericSubtitle->addCue((new GenericSubtitleCue())->setLines(['Jerry!'])->setTiming(100, 1200));

        $cues = $genericSubtitle->removeDuplicateCues()->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(100,  $cues[0]->getStartMs());
        $this->assertSame(1200, $cues[0]->getEndMs());
        $this->assertSame(['Summer of George'], $cues[0]->getLines());

        $this->assertSame(100, $cues[1]->getStartMs());
        $this->assertSame(600, $cues[1]->getEndMs());
        $this->assertSame(['Summer of George'], $cues[1]->getLines());

        $this->assertSame(100,  $cues[2]->getStartMs());
        $this->assertSame(1200, $cues[2]->getEndMs());
        $this->assertSame(['Jerry!'], $cues[2]->getLines());
    }
}
