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

        $content = app('TextFileReader')->getLines($filePath);

        $this->assertSame($content, $srt->getContentLines());
    }

    /** @test */
    function it_returns_empty_content_if_there_are_no_cues()
    {
        $srt = new Srt("{$this->testFilesStoragePath}TextFiles/empty.srt");

        $this->assertSame("", $srt->getContent());

        $this->assertSame([], $srt->getContentLines());
    }

    /** @test */
    function it_parses_edge_cases()
    {
        // Starts with a timing line
        $srt = new Srt("{$this->testFilesStoragePath}TextFiles/SubtitleParsing/parse-edge-case-1.srt");
        $cues = $srt->getCues();
        $this->assertEquals(5, count($cues));

        // Ends with a timing line, it isnt added because it has no text lines
        $srt = new Srt("{$this->testFilesStoragePath}TextFiles/SubtitleParsing/parse-edge-case-2.srt");
        $cues = $srt->getCues();
        $this->assertEquals(5, count($cues));

        // Starts with three timing lines in a row, and some random timings without text
        $srt = new Srt("{$this->testFilesStoragePath}TextFiles/SubtitleParsing/parse-edge-case-3.srt");
        $cues = $srt->getCues();
        $this->assertEquals(1, count($cues));
        $this->assertEquals($cues[0]->getLines()[0], "One of them,");
        $this->assertEquals($cues[0]->getLines()[1], "her total was $8.00.");
        $this->assertEquals(false, isset($cues[0]->getLines()[2]));

        // doesn't have a trailing empty line
        $srt = new Srt("{$this->testFilesStoragePath}TextFiles/SubtitleParsing/parse-edge-case-4.srt");
        $cues = $srt->getCues();
        $this->assertEquals(5, count($cues));
        $this->assertEquals($cues[4]->getLines()[0], "They both, of course,");
        $this->assertEquals($cues[4]->getLines()[1], "choose to pay");
        $this->assertEquals(false, isset($cues[4]->getLines()[2]));

        // doesn't have a trailing empty line
        $srt = new Srt("{$this->testFilesStoragePath}TextFiles/SubtitleParsing/parse-edge-case-5.srt");
        $cues = $srt->getCues();
        $this->assertEquals(5, count($cues));
        $this->assertEquals($cues[4]->getLines()[0], "They both, of course,");
        $this->assertEquals(false, isset($cues[4]->getLines()[1]));
    }

    /** @test */
    function it_shifts_cues()
    {
        $srt = new Srt("{$this->testFilesStoragePath}TextFiles/three-cues.srt");

        $this->assertSame(1266, $srt->getCues()[0]->getStartMs());
        $this->assertSame(3366, $srt->getCues()[0]->getEndMs());

        $this->assertSame(3400, $srt->getCues()[1]->getStartMs());
        $this->assertSame(6366, $srt->getCues()[1]->getEndMs());

        $this->assertSame(6400, $srt->getCues()[2]->getStartMs());
        $this->assertSame(8233, $srt->getCues()[2]->getEndMs());

        $srt->shift(1000);

        $this->assertSame(2266, $srt->getCues()[0]->getStartMs());
        $this->assertSame(4366, $srt->getCues()[0]->getEndMs());

        $this->assertSame(4400, $srt->getCues()[1]->getStartMs());
        $this->assertSame(7366, $srt->getCues()[1]->getEndMs());

        $this->assertSame(7400, $srt->getCues()[2]->getStartMs());
        $this->assertSame(9233, $srt->getCues()[2]->getEndMs());

        $srt->shift("-1000");

        $this->assertSame(1266, $srt->getCues()[0]->getStartMs());
        $this->assertSame(3366, $srt->getCues()[0]->getEndMs());

        $this->assertSame(3400, $srt->getCues()[1]->getStartMs());
        $this->assertSame(6366, $srt->getCues()[1]->getEndMs());

        $this->assertSame(6400, $srt->getCues()[2]->getStartMs());
        $this->assertSame(8233, $srt->getCues()[2]->getEndMs());
    }

    /** @test */
    function it_partial_shifts_cues()
    {
        $srt = new Srt("{$this->testFilesStoragePath}TextFiles/three-cues.srt");

        $this->assertSame(1266, $srt->getCues()[0]->getStartMs());
        $this->assertSame(3366, $srt->getCues()[0]->getEndMs());

        $this->assertSame(3400, $srt->getCues()[1]->getStartMs());
        $this->assertSame(6366, $srt->getCues()[1]->getEndMs());

        $this->assertSame(6400, $srt->getCues()[2]->getStartMs());
        $this->assertSame(8233, $srt->getCues()[2]->getEndMs());

        $srt->shiftPartial(0, 3500, 1000);

        $this->assertSame(2266, $srt->getCues()[0]->getStartMs());
        $this->assertSame(4366, $srt->getCues()[0]->getEndMs());

        $this->assertSame(4400, $srt->getCues()[1]->getStartMs());
        $this->assertSame(7366, $srt->getCues()[1]->getEndMs());

        $this->assertSame(6400, $srt->getCues()[2]->getStartMs());
        $this->assertSame(8233, $srt->getCues()[2]->getEndMs());

        $srt->shiftPartial(4400, 6500, "-1000");

        $this->assertSame(2266, $srt->getCues()[0]->getStartMs());
        $this->assertSame(4366, $srt->getCues()[0]->getEndMs());

        $this->assertSame(3400, $srt->getCues()[1]->getStartMs());
        $this->assertSame(6366, $srt->getCues()[1]->getEndMs());

        $this->assertSame(5400, $srt->getCues()[2]->getStartMs());
        $this->assertSame(7233, $srt->getCues()[2]->getEndMs());
    }
}