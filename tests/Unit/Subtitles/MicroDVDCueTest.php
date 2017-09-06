<?php

namespace Tests\Unit;

use App\Subtitles\PlainText\GenericSubtitleCue;
use App\Subtitles\PlainText\MicroDVDCue;
use Tests\TestCase;

class MicroDVDCueTest extends TestCase
{
    /** @test */
    function it_identifies_valid_timing_strings()
    {
        $this->assertTrue(MicroDVDCue::isTimingString('{1164}{1237}{y:i}¿ycie iœæ musi naprzód.'));
        $this->assertTrue(MicroDVDCue::isTimingString('{1440}{1489}DJANGO'));
        $this->assertTrue(MicroDVDCue::isTimingString('{0}{0}ZERO'));
        $this->assertTrue(MicroDVDCue::isTimingString('[170][195]- I have a wife.|- She\'s shut away in a tower.'));
        $this->assertTrue(MicroDVDCue::isTimingString('[0][6]{c:$0000ff}Subtitles downloaded by MiniSubDownloader:|{c:$ff7070}{y:u}http://minisubdownloader.org'));
    }

    /** @test */
    function it_rejects_invalid_timing_strings()
    {
        $this->assertFalse(MicroDVDCue::isTimingString(""));
        $this->assertFalse(MicroDVDCue::isTimingString(" "));
        $this->assertFalse(MicroDVDCue::isTimingString("{123}[456]Both brackets is wrong|man"));
        $this->assertFalse(MicroDVDCue::isTimingString("[123]{456}Both brackets is wrong|man"));
        $this->assertFalse(MicroDVDCue::isTimingString("{1164}{1237}")); // it needs at least 1 character of dialogue
    }

    /** @test */
    function it_rejects_timing_strings_that_end_before_they_start()
    {
        $this->assertFalse(MicroDVDCue::isTimingString("[100][50]好啊  朋友"));
        $this->assertFalse(MicroDVDCue::isTimingString("{100}{50}好啊  朋友"));
    }

    /** @test */
    function it_loads_from_string()
    {
        $cue = new MicroDVDCue();

        $cue->loadString("[521][551]- Based on whose information?|- Varys: <i>Ser Jorah Mormont.</i>");

        $this->assertSame(521, $cue->getStartMs());
        $this->assertSame(551, $cue->getEndMs());

        $this->assertSame([
            '- Based on whose information?',
            '- Varys: <i>Ser Jorah Mormont.</i>',
        ], $cue->getLines());
    }

    /** @test */
    function it_preserves_cues()
    {
        $valuesShouldNotChange = [
            '[521][551]- Based on whose information?|- Varys: <i>Ser Jorah Mormont.</i>',
            '[551][574]He is serving as advisor|to the Targaryens.',
            '[574][597]You bring us the whispers|of a traitor.',
            '{11465}{11544}Za godzinê przes³uchaj¹ mnie|osobisty sekretarz króla,',
            '{11544}{11683}trzej adwokaci i kilku urzêdników.|Bêd¹ chcieli mieæ to na piœmie.',
        ];

        foreach($valuesShouldNotChange as $value) {
            $this->assertSame($value, (new MicroDVDCue())->loadString($value)->toString());
        }
    }

    /** @test */
    function it_can_positive_shift_and_preserves_cues()
    {
        $cue = new MicroDVDCue();

        $cue->loadString("[3526][3553]( men chanting )|Guilty. Guilty. Guilty.")
            ->shift(1000);

        $this->assertSame("[4526][4553]( men chanting )|Guilty. Guilty. Guilty.", $cue->toString());
    }

    /** @test */
    function it_can_negative_shift_and_preserves_cues()
    {
        $cue = new MicroDVDCue();

        $cue->loadString("[4068][4087]Beric:|<i>He will.</i>")
            ->shift(-1000);

        $this->assertSame("[3068][3087]Beric:|<i>He will.</i>", $cue->toString());
    }

    /** @test */
    function timings_do_not_exceed_minimum_value()
    {
        $cue = new MicroDVDCue();

        $cue->loadString("[6804][6833]is that what lords do to|their ladies in the South?")
            ->shift(-99999999999999999);

        $this->assertSame("[0][0]is that what lords do to|their ladies in the South?", $cue->toString());
    }

    /** @test */
    function it_transforms_to_generic_cue()
    {
        $assCue = new MicroDVDCue();

        $assCue->loadString("{20170}{20256}Nigdy wczeœniej nie widzieli|czarnego na koniu.");

        $genericCue = $assCue->toGenericCue();

        $this->assertTrue($genericCue instanceof GenericSubtitleCue);

        $this->assertFalse($genericCue instanceof MicroDVDCue);

        $this->assertSame(20170, $genericCue->getStartMs());
        $this->assertSame(20256, $genericCue->getEndMs());

        $this->assertSame(['Nigdy wczeœniej nie widzieli', 'czarnego na koniu.'], $genericCue->getLines());
    }

    /** @test */
    function it_trims_timing_lines()
    {
        // regression test for a timing line that had a tab at the end.
        // ::isTimingString trimmed the line, but setTimingFromString didn't

        $cue = new MicroDVDCue();

        $this->assertTrue(MicroDVDCue::isTimingString("\t{16247}{16272}Pierwsza."));
        $this->assertTrue(MicroDVDCue::isTimingString("{16247}{16272}Pierwsza.\t"));

        $cue->setTimingFromString("\t{16247}{16272}Pierwsza.");
        $cue->setTimingFromString("{16247}{16272}Pierwsza.\t");
    }
}
