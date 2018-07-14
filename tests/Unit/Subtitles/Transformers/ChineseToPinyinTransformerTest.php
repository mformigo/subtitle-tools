<?php

namespace Tests\Unit\Subtitles\Transformers;

use App\Subtitles\PlainText\Srt;
use App\Subtitles\Transformers\ChineseToPinyinTransformer;
use Tests\TestCase;

class ChineseToPinyinTransformerTest extends TestCase
{
    /** @test */
    function it_transforms_cue_lines_to_pinyin()
    {
        $srt = new Srt("{$this->testFilesStoragePath}text/srt/three-cues-chinese.srt");

        $transformer = app(ChineseToPinyinTransformer::class);

        $madeChanges = $transformer->transformCues($srt);

        $this->assertTrue($madeChanges);

        $lines = $srt->getContentLines();

        $this->assertSame('wǒ yǐjīng kāishǐ xiǎng tā le', $lines[2]);

        $this->assertSame('hěn bàoqiàn wǒ méi néng qù xiànchǎng', $lines[6]);
        $this->assertSame('[AUDIENCE LAUGHS]', $lines[7]);

        $this->assertSame('tāmen zài zhìyí zìjǐ céngjīng zuò guò de shìqing', $lines[11]);
    }

    /** @test */
    function it_returns_false_if_no_changes_are_made()
    {
        $srt = new Srt("{$this->testFilesStoragePath}text/srt/three-cues.srt");

        $transformer = app(ChineseToPinyinTransformer::class);

        $linesBefore = $srt->getContentLines();

        $madeChanges = $transformer->transformCues($srt);

        $this->assertFalse($madeChanges);

        $linesAfter = $srt->getContentLines();

        $this->assertSame($linesBefore, $linesAfter);
    }
}
