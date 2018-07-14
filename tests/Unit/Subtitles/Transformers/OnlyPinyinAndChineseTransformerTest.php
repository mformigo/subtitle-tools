<?php

namespace Tests\Unit\Subtitles\Transformers;

use App\Subtitles\PlainText\Srt;
use App\Subtitles\Transformers\OnlyPinyinAndChineseTransformer;
use Tests\TestCase;

class OnlyPinyinAndChineseTransformerTest extends TestCase
{
    /** @test */
    function it_adds_pinyin_under_chinese_lines()
    {
        $srt = new Srt("{$this->testFilesStoragePath}TextFiles/three-cues-chinese.srt");

        $transformer = app(OnlyPinyinAndChineseTransformer::class);

        $madeChanges = $transformer->transformCues($srt);

        $this->assertTrue($madeChanges);

        $lines = $srt->getContentLines();

        $this->assertSame('我已经开始想他了', $lines[2]);
        $this->assertSame('wǒ yǐjīng kāishǐ xiǎng tā le', $lines[3]);

        $this->assertSame('很抱歉我没能去现场', $lines[7]);
        $this->assertSame('hěn bàoqiàn wǒ méi néng qù xiànchǎng', $lines[8]);

        $this->assertSame('他们在质疑自己曾经做过的事情', $lines[12]);
        $this->assertSame('tāmen zài zhìyí zìjǐ céngjīng zuò guò de shìqing', $lines[13]);
    }

    /** @test */
    function it_returns_false_if_no_changes_are_made()
    {
        $srt = new Srt("{$this->testFilesStoragePath}TextFiles/three-cues.srt");

        $transformer = app(OnlyPinyinAndChineseTransformer::class);

        $madeChanges = $transformer->transformCues($srt);

        $this->assertFalse($madeChanges);
    }
}
