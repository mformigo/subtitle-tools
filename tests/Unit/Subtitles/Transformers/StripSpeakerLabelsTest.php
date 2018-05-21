<?php

namespace Tests\Unit;

use App\Subtitles\PlainText\Srt;
use App\Subtitles\PlainText\SrtCue;
use App\Subtitles\Transformers\CueTransformer;
use App\Subtitles\Transformers\StripSpeakerLabels;
use Tests\TestCase;

class StripSpeakerLabelsTest extends TestCase
{
    protected $transformer = StripSpeakerLabels::class;

    protected function assertLinesTransformed($expected, $lines)
    {
        /** @var CueTransformer $transformer */
        $transformer = app()->make($this->transformer);

        $this->assertSame(
            array_wrap($expected),
            $actual = $transformer->transformLines(array_wrap($lines))
        );
    }

    protected function assertLinesNotTransformed($lines)
    {
        $this->assertLinesTransformed($lines, $lines);
    }

    /** @test */
    function it_strips_fully_uppercase_speaker_labels()
    {
        $this->assertLinesTransformed([
            'Oh, girls. Yes, go, go.',
        ], [
            'BLUE:',
            'Oh, girls. Yes, go, go.',
        ]);

        $this->assertLinesTransformed([
            'She looked nice.',
            'She looked stuck-up to me.',
        ], [
            'AMBER: She looked nice.',
            'BLONDIE: She looked stuck-up to me.',
        ]);

        $this->assertLinesTransformed(
            'Hey. Come here.',
            'WISE MAN: Hey. Come here.'
        );
    }

    /** @test */
    function it_strips_mostly_uppercase_speaker_labels()
    {
        $this->assertLinesTransformed(
            'She looked stuck-up to me.',
            'BLONDiE: She looked stuck-up to me.'
        );
    }

    /** @test */
    function it_does_not_strip_normal_dialogue()
    {
        $this->assertLinesNotTransformed('Warning: Weapon will activate soon!');

        $this->assertLinesNotTransformed('And finally, this question:');

        $this->assertLinesNotTransformed('It\'s 1:00 in the morning.');

        $this->assertLinesNotTransformed('- It\'s 5:01. - Which makes you?');
    }

    /** @test */
    function it_does_not_strip_weird_lines()
    {
        $this->assertLinesNotTransformed('-=伊甸园美剧 http://bbs.sfile2012.com=-');

        $this->assertLinesNotTransformed('校对: 不辣的皮特');

        $this->assertLinesNotTransformed('我们就可以在你墓碑上写:');
    }

    /** @test */
    function it_does_not_strip_fully_numeric_speaker_labels()
    {
        $this->assertLinesNotTransformed('1993: Great year for humanity!');
    }

    /** @test */
    function it_returns_a_bool_based_on_if_something_was_transformed_in_the_cue()
    {
        $transformer = new StripSpeakerLabels();

        $this->assertTrue($transformer->transformCue(
            (new SrtCue)->addLine('MAN: this will get transformed')
        ));

        $this->assertFalse($transformer->transformCue(
            (new SrtCue)->addLine('And finally, this question:')
        ));
    }

    /** @test */
    function it_returns_a_bool_based_on_if_something_was_transformed_in_the_subtitle()
    {
        $transformer = new StripSpeakerLabels();

        $srt = new Srt();

        $srt->addCue(
            (new SrtCue)->addLine('And finally, this question:')
        );

        $this->assertFalse($transformer->transformCues($srt));

        $srt->addCue(
            (new SrtCue)->addLine('MAN: this will get transformed')
        );

        $this->assertTrue($transformer->transformCues($srt));
    }
}
