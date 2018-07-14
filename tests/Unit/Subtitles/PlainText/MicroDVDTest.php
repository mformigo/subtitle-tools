<?php

namespace Tests\Unit\Subtitles\PlainText;

use App\Subtitles\PlainText\GenericSubtitle;
use App\Subtitles\PlainText\MicroDVD;
use Tests\TestCase;

class MicroDVDTest extends TestCase
{
    /** @test */
    function it_loads_from_file()
    {
        $microDVD = new MicroDVD($this->testFilesStoragePath.'text/microdvd/three-cues.sub');

        $this->assertSame('three-cues', $microDVD->getFileNameWithoutExtension());

        $this->assertSame($this->testFilesStoragePath.'text/microdvd/three-cues.sub', $microDVD->getFilePath());
    }

    /** @test */
    function it_has_a_good_default_frame_rate()
    {
        $this->assertSame(
            23.976,
            (new MicroDVD)->getFps()
        );
    }

    /** @test */
    function it_reads_frame_rate_hints()
    {
        $microDVD = new MicroDVD($this->testFilesStoragePath.'text/microdvd/microdvd-fps-hint.sub');

        $this->assertSame(25.0, $microDVD->getFps());

        $microDVD = new MicroDVD($this->testFilesStoragePath.'text/microdvd/microdvd02.sub');

        $this->assertSame(23.976, $microDVD->getFps());
    }

    /** @test */
    function it_transforms_to_generic_subtitle()
    {
        $microDVD = new MicroDVD($this->testFilesStoragePath.'text/microdvd/three-cues.sub');

        $this->assertSame(23.976, $microDVD->getFps());

        $genericSub = $microDVD->toGenericSubtitle();

        $genericCues = $genericSub->getCues();

        $this->assertTrue($genericSub instanceof GenericSubtitle && ! $genericSub instanceof MicroDVD);

        $this->assertSame($this->testFilesStoragePath.'text/microdvd/three-cues.sub', $genericSub->getFilePath());

        $this->assertSame('three-cues', $genericSub->getFileNameWithoutExtension());

        $this->assertCount(3, $genericCues);

        $this->assertSame(12429, $genericCues[0]->getStartMs());
        $this->assertSame(16058, $genericCues[0]->getEndMs());
        $this->assertSame(['- I decorated it myself.', '- Get out!'], $genericCues[0]->getLines());

        $this->assertSame(170838, $genericCues[1]->getStartMs());
        $this->assertSame(176301, $genericCues[1]->getEndMs());
        $this->assertSame(['Wait a minute. I claimed you', 'in the name of France four years ago.'], $genericCues[1]->getLines());

        $this->assertSame(1282658, $genericCues[2]->getStartMs());
        $this->assertSame(1287371, $genericCues[2]->getEndMs());
        $this->assertSame(['{y:i}But when I dial the telephone'], $genericCues[2]->getLines());
    }
}
