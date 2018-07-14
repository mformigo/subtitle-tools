<?php

namespace Tests\Unit\Subtitles\PlainText;

use App\Subtitles\PlainText\GenericSubtitleCue;
use App\Subtitles\PlainText\Mpl2Cue;
use Tests\TestCase;

class Mpl2CueTest extends TestCase
{
    /** @test */
    function it_identifies_valid_timing_strings()
    {
        $this->assertTrue(Mpl2Cue::isTimingString('[1164][1237]{y:i}¿ycie iœæ musi naprzód.'));
        $this->assertTrue(Mpl2Cue::isTimingString('[1440][1489]DJANGO'));
        $this->assertTrue(Mpl2Cue::isTimingString('[0][0]ZERO'));
    }

    /** @test */
    function it_rejects_invalid_timing_strings()
    {
        $this->assertFalse(Mpl2Cue::isTimingString(""));
        $this->assertFalse(Mpl2Cue::isTimingString(" "));
        $this->assertFalse(Mpl2Cue::isTimingString("{123}{456}not curly brackets|man"));
        $this->assertFalse(Mpl2Cue::isTimingString("[1164][1237]")); // it needs at least 1 character of dialogue
    }

    /** @test */
    function it_rejects_timing_strings_that_end_before_they_start()
    {
        $this->assertFalse(Mpl2Cue::isTimingString("[100][50]好啊  朋友"));
    }

    /** @test */
    function it_loads_from_string()
    {
        $cue = new Mpl2Cue();

        $cue->loadString("[521][551]- Based on whose information?|/- Varys: Ser Jorah Mormont.");

        $this->assertSame(52100, $cue->getStartMs());
        $this->assertSame(55100, $cue->getEndMs());

        $this->assertSame([
            '- Based on whose information?',
            '/- Varys: Ser Jorah Mormont.',
        ], $cue->getLines());
    }

    /** @test */
    function it_preserves_cues()
    {
        $valuesShouldNotChange = [
            '[521][551]- Based on whose information?|- Varys: <i>Ser Jorah Mormont.</i>',
            '[551][574]/He is serving as advisor|to the Targaryens.',
            '[574][597]You bring us the whispers|of a traitor.',
            '[11465][11544]Za godzinê przes³uchaj¹ mnie|osobisty sekretarz króla,',
            '[11544][11683]trzej adwokaci i kilku urzêdników.|Bêd¹ chcieli mieæ to na piœmie.',
        ];

        foreach($valuesShouldNotChange as $value) {
            $this->assertSame($value, (new Mpl2Cue())->loadString($value)->toString());
        }
    }

    /** @test */
    function it_transforms_to_generic_cue()
    {
        $mpl2Cue = new Mpl2Cue();

        $mpl2Cue->loadString("[20170][20256]/Nigdy wczeœniej nie widzieli|czarnego na koniu.");

        $genericCue = $mpl2Cue->toGenericCue();

        $this->assertTrue($genericCue instanceof GenericSubtitleCue);

        $this->assertFalse($genericCue instanceof Mpl2Cue);

        $this->assertSame(2017000, $genericCue->getStartMs());
        $this->assertSame(2025600, $genericCue->getEndMs());

        $this->assertSame(['<i>Nigdy wczeœniej nie widzieli</i>', 'czarnego na koniu.'], $genericCue->getLines());
    }

    /** @test */
    function it_trims_timing_lines()
    {
        // regression test for a timing line that had a tab at the end.
        // ::isTimingString trimmed the line, but setTimingFromString didn't

        $cue = new Mpl2Cue();

        $this->assertTrue(Mpl2Cue::isTimingString("\t[16247][16272]Pierwsza."));
        $this->assertTrue(Mpl2Cue::isTimingString("[16247][16272]Pierwsza.\t"));

        $cue->setTimingFromString("\t[16247][16272]Pierwsza.");
        $cue->setTimingFromString("[16247][16272]Pierwsza.\t");
    }
}
