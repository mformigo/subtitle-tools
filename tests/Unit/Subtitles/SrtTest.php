<?php

namespace Tests\Unit;

use App\Subtitles\PlainText\Srt;
use Tests\TestCase;

class SrtTest extends TestCase
{
    /** @test */
    function it_loads_from_file()
    {
        $srt = new Srt("{$this->testFilesStoragePath}TextFiles/three-cues.srt");

        $this->assertSame('three-cues', $srt->getFileNameWithoutExtension());

        $cues = $srt->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(1266, $cues[0]->getStartMs());
        $this->assertSame(3366, $cues[0]->getEndMs());
        $this->assertSame(['Do you know what this is all', 'about? Why we\'re here?'], $cues[0]->getLines());

        $this->assertSame(3400, $cues[1]->getStartMs());
        $this->assertSame(6366, $cues[1]->getEndMs());
        $this->assertSame(['To be out. This is out.', '[AUDIENCE LAUGHS]'], $cues[1]->getLines());

        $this->assertSame(6400, $cues[2]->getStartMs());
        $this->assertSame(8233, $cues[2]->getEndMs());
        $this->assertSame(['And out is one of', 'the single most'], $cues[2]->getLines());
    }

    /** @test */
    function it_preserves_valid_srt_files()
    {
        $filePath = "{$this->testFilesStoragePath}TextFiles/three-cues.srt";

        $srt = new Srt($filePath);

        $content = app('TextFileReader')->getContents($filePath);

        $this->assertSame($content, $srt->getContent());
    }

    /** @test */
    function it_returns_empty_content_if_there_are_no_cues()
    {
        $srt = new Srt("{$this->testFilesStoragePath}TextFiles/empty.srt");

        $this->assertSame("", $srt->getContent());

        $this->assertSame([], $srt->getContentLines());
    }
}