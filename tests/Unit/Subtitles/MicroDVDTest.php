<?php

namespace Tests\Unit;

use App\Subtitles\PartialShiftsCues;
use App\Subtitles\PlainText\GenericSubtitle;
use App\Subtitles\PlainText\MicroDVD;
use App\Subtitles\ShiftsCues;
use Tests\TestCase;

class MicroDVDTest extends TestCase
{
    /** @test */
    function it_loads_from_file()
    {
        $microDVD = new MicroDVD("{$this->testFilesStoragePath}TextFiles/three-cues.sub");

        $this->assertSame('three-cues', $microDVD->getFileNameWithoutExtension());

        $this->assertSame("{$this->testFilesStoragePath}TextFiles/three-cues.sub", $microDVD->getFilePath());
    }

    /** @test */
    function it_transforms_to_generic_subtitle()
    {
        $microDVD = new MicroDVD("{$this->testFilesStoragePath}TextFiles/three-cues.sub");

        $genericSub = $microDVD->toGenericSubtitle();

        $genericCues = $genericSub->getCues();

        $this->assertTrue($genericSub instanceof GenericSubtitle && !$genericSub instanceof MicroDVD);

        $this->assertSame("{$this->testFilesStoragePath}TextFiles/three-cues.sub", $genericSub->getFilePath());

        $this->assertSame("three-cues", $genericSub->getFileNameWithoutExtension());

        $this->assertSame(3, count($genericCues));

        $this->assertSame(298, $genericCues[0]->getStartMs());
        $this->assertSame(385, $genericCues[0]->getEndMs());
        $this->assertSame(['- I decorated it myself.', '- Get out!'], $genericCues[0]->getLines());

        $this->assertSame(4096, $genericCues[1]->getStartMs());
        $this->assertSame(4227, $genericCues[1]->getEndMs());
        $this->assertSame(['Wait a minute. I claimed you', 'in the name of France four years ago.'], $genericCues[1]->getLines());

        $this->assertSame(30753, $genericCues[2]->getStartMs());
        $this->assertSame(30866, $genericCues[2]->getEndMs());
        $this->assertSame(['{y:i}But when I dial the telephone'], $genericCues[2]->getLines());
    }

    /** @test */
    function it_shifts_cues()
    {
        $microDVD = new MicroDVD("{$this->testFilesStoragePath}TextFiles/three-cues.sub");

        $this->assertTrue($microDVD instanceof ShiftsCues);

        $originalLines = $microDVD->getContentLines();

        $cues = $microDVD->toGenericSubtitle()->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(298, $cues[0]->getStartMs());
        $this->assertSame(385, $cues[0]->getEndMs());

        $this->assertSame(4096, $cues[1]->getStartMs());
        $this->assertSame(4227, $cues[1]->getEndMs());

        $this->assertSame(30753, $cues[2]->getStartMs());
        $this->assertSame(30866, $cues[2]->getEndMs());

        $microDVD->shift(1000);

        $cues = $microDVD->toGenericSubtitle()->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(1298, $cues[0]->getStartMs());
        $this->assertSame(1385, $cues[0]->getEndMs());

        $this->assertSame(5096, $cues[1]->getStartMs());
        $this->assertSame(5227, $cues[1]->getEndMs());

        $this->assertSame(31753, $cues[2]->getStartMs());
        $this->assertSame(31866, $cues[2]->getEndMs());

        $microDVD->shift("-1000");

        $cues = $microDVD->toGenericSubtitle()->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(298, $cues[0]->getStartMs());
        $this->assertSame(385, $cues[0]->getEndMs());

        $this->assertSame(4096, $cues[1]->getStartMs());
        $this->assertSame(4227, $cues[1]->getEndMs());

        $this->assertSame(30753, $cues[2]->getStartMs());
        $this->assertSame(30866, $cues[2]->getEndMs());

        $this->assertSame($originalLines, $microDVD->getContentLines());
    }

    /** @test */
    function it_partial_shifts_cues()
    {
        $microDVD = new MicroDVD("{$this->testFilesStoragePath}TextFiles/three-cues.sub");

        $this->assertTrue($microDVD instanceof PartialShiftsCues);

        $cues = $microDVD->toGenericSubtitle()->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(298, $cues[0]->getStartMs());
        $this->assertSame(385, $cues[0]->getEndMs());

        $this->assertSame(4096, $cues[1]->getStartMs());
        $this->assertSame(4227, $cues[1]->getEndMs());

        $this->assertSame(30753, $cues[2]->getStartMs());
        $this->assertSame(30866, $cues[2]->getEndMs());

        $microDVD->shiftPartial(0, 1000, 1000);

        $cues = $microDVD->toGenericSubtitle()->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(1298, $cues[0]->getStartMs());
        $this->assertSame(1385, $cues[0]->getEndMs());

        $this->assertSame(4096, $cues[1]->getStartMs());
        $this->assertSame(4227, $cues[1]->getEndMs());

        $this->assertSame(30753, $cues[2]->getStartMs());
        $this->assertSame(30866, $cues[2]->getEndMs());

        $microDVD->shiftPartial(3000, 6000, "-1000");

        $cues = $microDVD->toGenericSubtitle()->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(1298, $cues[0]->getStartMs());
        $this->assertSame(1385, $cues[0]->getEndMs());

        $this->assertSame(3096, $cues[1]->getStartMs());
        $this->assertSame(3227, $cues[1]->getEndMs());

        $this->assertSame(30753, $cues[2]->getStartMs());
        $this->assertSame(30866, $cues[2]->getEndMs());
    }
}
