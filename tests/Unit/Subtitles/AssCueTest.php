<?php

namespace Tests\Unit;

use App\Subtitles\PlainText\AssCue;
use App\Subtitles\PlainText\GenericSubtitleCue;
use Tests\TestCase;

class AssCueTest extends TestCase
{
    /** @test */
    function it_identifies_valid_timing_strings()
    {
        $this->assertTrue(AssCue::isTimingString("Dialogue: 0,0:00:08.73,0:00:38.73,*Default,NTP,0000,0000,0000,,{\\an7\\p1\\bord0\\shad0\\fscx0\\fscy0\\t(0,200,\\fscx206\\fscy266\\t(200,400,\\fscx103\\fscy133\\t(29600,29800,\\fscx206,fscy266\\t(29800,30000,\\fscx0\\fscy0))))\\c&H573E00&\\pos(39.5,211)}m -13 -10 l -13 -13 l 10 -13 l -6 3 l -8 1 l 3 -10 l -13 -10m -13 -14 l 13 -14 l 13 12 l -13 12 l -13 -14 {\\p0}"));
        $this->assertTrue(AssCue::isTimingString("Dialogue: 1,0:03:26.73,0:03:27.59,*Default,NTP,0000,0000,0000,,你冷静的\\N{\fn微软雅黑\\fs14}Come on! Hey!"));
        $this->assertTrue(AssCue::isTimingString("Dialogue: 10,0:08:26.11,0:08:27.56,*Default,NTP,0000,0000,0000,,就拿罗德尼来说吧\\N{\\fn微软雅黑\\fs14}What about Roddney, huh?"));
        $this->assertTrue(AssCue::isTimingString("Dialogue: 100,0:29:09.52,0:29:10.45,*Default,NTP,0000,0000,0000,,斯宾塞\\N{\fn微软雅黑\\fs14}Spence?"));
        $this->assertTrue(AssCue::isTimingString("Dialogue: 0,0:00:00.78,0:00:01.99,*Default,NTP,0,0,0,,好啊  朋友"));
        $this->assertTrue(AssCue::isTimingString("Dialogue: 0,1:02:35.39,1:02:37.00,*Default,NTP,0,0,0,,請馬上上車"));
        $this->assertTrue(AssCue::isTimingString("Dialogue: 0,1:02:35.39,1:02:37.00,,,,,,,"));
        $this->assertTrue(AssCue::isTimingString("Dialogue: 0,1:02:35.39,1:02:37.00,,,,,,,,"));
    }

    /** @test */
    function it_rejects_invalid_timing_strings()
    {
        $this->assertFalse(AssCue::isTimingString(""));
        $this->assertFalse(AssCue::isTimingString(" "));
        $this->assertFalse(AssCue::isTimingString("Dialogue: "));
        $this->assertFalse(AssCue::isTimingString("Dialogue: wow, this is amazing"));
        $this->assertFalse(AssCue::isTimingString(null));
        $this->assertFalse(AssCue::isTimingString("0,0:00:04.19,0:00:08.15,"));
        $this->assertFalse(AssCue::isTimingString("Style: Default,方正黑体_GBK,20,&H00FFFFFF,&HF0000000,&H00000000,&H32000000,0,0,0,0,100,100,0,0,1,2,1,2,5,5,2,134"));
        $this->assertFalse(AssCue::isTimingString("Dialogue:0,1:02:35.39,1:02:37.00,*Default,NTP,0,0,0,,請馬上上車"));
        $this->assertFalse(AssCue::isTimingString("Dialogue: 0,01:02:35.39,01:02:37.00,*Default,NTP,0,0,0,,請馬上上車"));
        $this->assertFalse(AssCue::isTimingString("Dialogue: 0,1:02:35.39,01:02:37.00,*Default,NTP,0,0,0,,請馬上上車"));
        $this->assertFalse(AssCue::isTimingString("Dialogue: 0,01:02:35.39,1:02:37.00,*Default,NTP,0,0,0,,請馬上上車"));
        $this->assertFalse(AssCue::isTimingString("Dialogue: 0,1:02:35,39,1:02:37,00,*Default,NTP,0,0,0,,請馬上上車"));
        $this->assertFalse(AssCue::isTimingString("Dialogue: 0,1:02:35.3,1:02:37.0,*Default,NTP,0,0,0,,請馬上上車"));
        $this->assertFalse(AssCue::isTimingString("dialogue: -1,1:02:35.39,1:02:37.00,*Default,NTP,0,0,0,,請馬上上車"));
        $this->assertFalse(AssCue::isTimingString("Dialogue: R,0:00:00.78,0:00:01.99,*Default,NTP,0,0,0,,好啊  朋友"));

        // should have at least 9 commas
        $this->assertFalse(AssCue::isTimingString("Dialogue: 0,1:02:35.39,1:02:37.00,*Default,NTP,0,0,,請馬上上車"));
        $this->assertFalse(AssCue::isTimingString("Dialogue: 0,1:02:35.39,1:02:37.00,*Default,NTP,0,,請馬上上車"));
        $this->assertFalse(AssCue::isTimingString("Dialogue: 0,1:02:35.39,1:02:37.00,*Default,NTP,請馬上上車"));
        $this->assertFalse(AssCue::isTimingString(",,,,,,,,,"));
        $this->assertFalse(AssCue::isTimingString("Dialogue: ,,,,,,,,,"));
        $this->assertFalse(AssCue::isTimingString(",,,,,,,,,,"));
    }

    /** @test */
    function it_rejects_timing_strings_that_end_before_they_start()
    {
        $this->assertFalse(AssCue::isTimingString("Dialogue: 0,0:00:01.99,0:00:00.78,*Default,NTP,0,0,0,,好啊  朋友"));
    }

    /** @test */
    function it_has_a_valid_default_value()
    {
        $cue = new AssCue();

        $this->assertTrue(AssCue::isTimingString($cue->toString()));
    }

    /** @test */
    function it_loads_from_string()
    {
        $cue = new AssCue();

        $cue->loadString("Dialogue: 0,0:00:00.78,0:00:01.99,*Default,NTP,0,0,0,,好啊, 朋友\NCrazy");

        $this->assertSame(780, $cue->getStartMs());
        $this->assertSame(1990, $cue->getEndMs());

        $this->assertSame([
            '好啊, 朋友',
            'Crazy',
        ], $cue->getLines());
    }

    /** @test */
    function it_preserves_cues()
    {
        $valuesShouldNotChange = [
            "Dialogue: 0,1:11:11.11,2:22:22.22,3,4,5,6,7,8,9",
            "Dialogue: 0,0:00:08.73,0:00:38.73,*Default,NTP,0000,0000,0000,,{\\an7\\p1\\bord0\\shad0\\fscx0\\fscy0\\t(0,200,\\fscx206\\fscy266\\t(200,400,\\fscx103\\fscy133\\t(29600,29800,\\fscx206,fscy266\\t(29800,30000,\\fscx0\\fscy0))))\\c&H573E00&\\pos(39.5,211)}m -13 -10 l -13 -13 l 10 -13 l -6 3 l -8 1 l 3 -10 l -13 -10m -13 -14 l 13 -14 l 13 12 l -13 12 l -13 -14 {\\p0}",
            "Dialogue: 1,0:03:26.73,0:03:27.59,*Default,NTP,0000,0000,0000,,你冷静的\\N{\fn微软雅黑\\fs14}Come on! Hey!",
            "Dialogue: 10,0:08:26.11,0:08:27.56,*Default,NTP,0000,0000,0000,,就拿罗德尼来说吧\\N{\\fn微软雅黑\\fs14}What about Roddney, huh?",
            "Dialogue: 100,0:29:09.52,0:29:10.45,*Default,NTP,0000,0000,0000,,斯宾塞\\N{\fn微软雅黑\\fs14}Spence?",
            "Dialogue: 0,0:00:00.78,0:00:01.99,*Default,NTP,0,0,0,,好啊  朋友",
            "Dialogue: 0,1:02:35.39,1:02:37.00,*Default,NTP,0,0,0,,請馬上上車",
            "Dialogue: 0,1:02:35.39,1:02:37.00,,,,,,,",
            "Dialogue: 0,1:02:35.39,1:02:37.00,,,,,,,,",
        ];

        foreach($valuesShouldNotChange as $value) {
            $this->assertSame($value, (new AssCue())->loadString($value)->toString());
        }
    }

    /** @test */
    function it_can_positive_shift_and_preserves_cues()
    {
        $cue = new AssCue();

        $cue->loadString("Dialogue: 1,0:03:26.73,0:03:27.59,*Default,NTP,0000,0000,0000,,你冷静的\\N{\fn微软雅黑\\fs14}Come on! Hey!")
            ->shift(1000);

        $this->assertSame("Dialogue: 1,0:03:27.73,0:03:28.59,*Default,NTP,0000,0000,0000,,你冷静的\\N{\fn微软雅黑\\fs14}Come on! Hey!", $cue->toString());
    }

    /** @test */
    function it_can_negative_shift_and_preserves_cues()
    {
        $cue = new AssCue();

        $cue->loadString("Dialogue: 1,0:03:26.73,0:03:27.59,*Default,NTP,0000,0000,0000,,你冷静的\\N{\fn微软雅黑\\fs14}Come on! Hey!")
            ->shift(-1000);

        $this->assertSame("Dialogue: 1,0:03:25.73,0:03:26.59,*Default,NTP,0000,0000,0000,,你冷静的\\N{\fn微软雅黑\\fs14}Come on! Hey!", $cue->toString());
    }

    /** @test */
    function timings_do_not_exceed_maximum_value()
    {
        $cue = new AssCue();

        $cue->loadString("Dialogue: 1,0:03:26.73,0:03:27.59,*Default,NTP,0000,0000,0000,,THE SUMMER OR GEORGE")
            ->shift(99999999999999999);

        $this->assertSame("Dialogue: 1,9:59:59.99,9:59:59.99,*Default,NTP,0000,0000,0000,,THE SUMMER OR GEORGE", $cue->toString());
    }

    /** @test */
    function timings_do_not_exceed_minimum_value()
    {
        $cue = new AssCue();

        $cue->loadString("Dialogue: 1,0:03:26.73,0:03:27.59,*Default,NTP,0000,0000,0000,,THE SUMMER OR GEORGE")
            ->shift(-99999999999999999);

        $this->assertSame("Dialogue: 1,0:00:00.00,0:00:00.00,*Default,NTP,0000,0000,0000,,THE SUMMER OR GEORGE", $cue->toString());
    }

    /** @test */
    function it_transforms_to_generic_cue()
    {
        $assCue = new AssCue();

        $assCue->loadString("Dialogue: 0,0:00:00.78,0:00:01.99,*Default,NTP,0,0,0,,好啊, 朋友\NCrazy");

        $genericCue = $assCue->toGenericCue();

        $this->assertTrue($genericCue instanceof GenericSubtitleCue);

        $this->assertFalse($genericCue instanceof AssCue);

        $this->assertSame(780, $genericCue->getStartMs());
        $this->assertSame(1990, $genericCue->getEndMs());

        $this->assertSame(['好啊, 朋友', 'Crazy'], $genericCue->getLines());
    }
}
