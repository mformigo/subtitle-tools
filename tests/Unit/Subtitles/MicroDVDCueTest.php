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
    }

    /** @test */
    function it_rejects_invalid_timing_strings()
    {
        $this->assertFalse(MicroDVDCue::isTimingString(""));
        $this->assertFalse(MicroDVDCue::isTimingString(" "));
        $this->assertFalse(MicroDVDCue::isTimingString("[123][456]not curly brackets|man"));
        $this->assertFalse(MicroDVDCue::isTimingString("{1164}{1237}")); // it needs at least 1 character of dialogue
    }

    /** @test */
    function it_rejects_timing_strings_that_end_before_they_start()
    {
        $this->assertFalse(MicroDVDCue::isTimingString("{100}{50}好啊  朋友"));
    }

    /** @test */
    function it_loads_from_string()
    {
        $cue = new MicroDVDCue();

        $cue->loadString("{521}{551}- Based on whose information?|- Varys: <i>Ser Jorah Mormont.</i>");

        $this->assertSame(21730, $cue->getStartMs());
        $this->assertSame(22981, $cue->getEndMs());

        $this->assertSame([
            '- Based on whose information?',
            '- Varys: <i>Ser Jorah Mormont.</i>',
        ], $cue->getLines());
    }

    /** @test */
    function it_preserves_cues()
    {
        $valuesShouldNotChange = [
            '{521}{551}- Based on whose information?|- Varys: <i>Ser Jorah Mormont.</i>',
            '{551}{574}He is serving as advisor|to the Targaryens.',
            '{574}{597}You bring us the whispers|of a traitor.',
            '{11465}{11544}Za godzinê przes³uchaj¹ mnie|osobisty sekretarz króla,',
            '{11544}{11683}trzej adwokaci i kilku urzêdników.|Bêd¹ chcieli mieæ to na piœmie.',
        ];

        foreach($valuesShouldNotChange as $value) {
            $this->assertSame($value, (new MicroDVDCue())->loadString($value)->toString());
        }
    }

    /** @test */
    function it_transforms_to_generic_cue()
    {
        $microDvdCue = new MicroDVDCue();

        $microDvdCue->loadString("{20170}{20256}Nigdy wczeœniej nie widzieli|czarnego na koniu.");

        $this->assertSame(23.976, $microDvdCue->getFps());

        $genericCue = $microDvdCue->toGenericCue();

        $this->assertTrue($genericCue instanceof GenericSubtitleCue);

        $this->assertFalse($genericCue instanceof MicroDVDCue);

        $this->assertSame(841258, $genericCue->getStartMs());
        $this->assertSame(844845, $genericCue->getEndMs());

        $this->assertSame(['Nigdy wczeœniej nie widzieli', 'czarnego na koniu.'], $genericCue->getLines());
    }

    /** @test */
    function it_calculates_start_ms_with_frame_rate()
    {
        $microDvdCue = new MicroDVDCue();

        $microDvdCue->loadString("{1050}{1250}Nigdy wczeœniej nie widzieli|czarnego na koniu.");

        $this->assertSame(23.976, $microDvdCue->getFps());

        $this->assertSame(43794, $microDvdCue->getStartMs());
        $this->assertSame(52135, $microDvdCue->getEndMs());

        $microDvdCue->setFps("25.0");

        $this->assertSame(25.0, $microDvdCue->getFps());

        $this->assertSame(42000, $microDvdCue->getStartMs());
        $this->assertSame(50000, $microDvdCue->getEndMs());
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
