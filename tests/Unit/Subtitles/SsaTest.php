<?php

namespace Tests\Unit;

use App\Subtitles\PartialShiftsCues;
use App\Subtitles\PlainText\GenericSubtitle;
use App\Subtitles\PlainText\Ssa;
use App\Subtitles\ShiftsCues;
use Tests\TestCase;

class SsaTest extends TestCase
{
    /** @test */
    function it_transforms_to_generic_subtitle()
    {
        $ssa = new Ssa();

        $ssa->loadFile("{$this->testFilesStoragePath}TextFiles/three-cues.ssa");

        $genericSub = $ssa->toGenericSubtitle();

        $genericCues = $genericSub->getCues();

        $this->assertTrue($genericSub instanceof GenericSubtitle && !$genericSub instanceof Ssa);

        $this->assertSame("{$this->testFilesStoragePath}TextFiles/three-cues.ssa", $genericSub->getFilePath());

        $this->assertSame("three-cues", $genericSub->getFileNameWithoutExtension());

        $this->assertSame(3, count($genericCues));

        $this->assertSame(0,     $genericCues[0]->getStartMs());
        $this->assertSame(38730, $genericCues[0]->getEndMs());
        $this->assertSame(['This is the first line, it is crazy'], $genericCues[0]->getLines());

        $this->assertSame(59730, $genericCues[1]->getStartMs());
        $this->assertSame(60000, $genericCues[1]->getEndMs());
        $this->assertSame(['Second line starts here', 'Also crazy'], $genericCues[1]->getLines());

        $this->assertSame(60250,  $genericCues[2]->getStartMs());
        $this->assertSame(600000, $genericCues[2]->getEndMs());
        $this->assertSame(['And this is the third line'], $genericCues[2]->getLines());
    }

    /** @test */
    function it_shifts_cues()
    {
        $ssa = new Ssa();

        $this->assertTrue($ssa instanceof ShiftsCues);

        $ssa->loadFile("{$this->testFilesStoragePath}TextFiles/three-cues.ssa");

        $originalLines = $ssa->getContentLines();

        $cues = $ssa->toGenericSubtitle()->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(0,     $cues[0]->getStartMs());
        $this->assertSame(38730, $cues[0]->getEndMs());

        $this->assertSame(59730, $cues[1]->getStartMs());
        $this->assertSame(60000, $cues[1]->getEndMs());

        $this->assertSame(60250,  $cues[2]->getStartMs());
        $this->assertSame(600000, $cues[2]->getEndMs());

        $ssa->shift(1000);

        $cues = $ssa->toGenericSubtitle()->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(1000,  $cues[0]->getStartMs());
        $this->assertSame(39730, $cues[0]->getEndMs());

        $this->assertSame(60730, $cues[1]->getStartMs());
        $this->assertSame(61000, $cues[1]->getEndMs());

        $this->assertSame(61250,  $cues[2]->getStartMs());
        $this->assertSame(601000, $cues[2]->getEndMs());

        $ssa->shift("-1000");

        $cues = $ssa->toGenericSubtitle()->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(0,     $cues[0]->getStartMs());
        $this->assertSame(38730, $cues[0]->getEndMs());

        $this->assertSame(59730, $cues[1]->getStartMs());
        $this->assertSame(60000, $cues[1]->getEndMs());

        $this->assertSame(60250,  $cues[2]->getStartMs());
        $this->assertSame(600000, $cues[2]->getEndMs());

        $this->assertSame($originalLines, $ssa->getContentLines());
    }

    /** @test */
    function it_partial_shifts_cues()
    {
        $ssa = new Ssa();

        $this->assertTrue($ssa instanceof PartialShiftsCues);

        $ssa->loadFile("{$this->testFilesStoragePath}TextFiles/three-cues.ssa");

        $cues = $ssa->toGenericSubtitle()->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(0,     $cues[0]->getStartMs());
        $this->assertSame(38730, $cues[0]->getEndMs());

        $this->assertSame(59730, $cues[1]->getStartMs());
        $this->assertSame(60000, $cues[1]->getEndMs());

        $this->assertSame(60250,  $cues[2]->getStartMs());
        $this->assertSame(600000, $cues[2]->getEndMs());

        $ssa->shiftPartial(0, 40000, 1000);
        $cues = $ssa->toGenericSubtitle()->getCues();

        $this->assertSame(1000,  $cues[0]->getStartMs());
        $this->assertSame(39730, $cues[0]->getEndMs());

        $this->assertSame(59730, $cues[1]->getStartMs());
        $this->assertSame(60000, $cues[1]->getEndMs());

        $this->assertSame(60250,  $cues[2]->getStartMs());
        $this->assertSame(600000, $cues[2]->getEndMs());

        $ssa->shiftPartial(60000, 700000, "-1000");
        $cues = $ssa->toGenericSubtitle()->getCues();

        $this->assertSame(1000,  $cues[0]->getStartMs());
        $this->assertSame(39730, $cues[0]->getEndMs());

        // Last two cues got swapped because they are sorted by start time
        $this->assertSame(59250,  $cues[1]->getStartMs());
        $this->assertSame(599000, $cues[1]->getEndMs());

        $this->assertSame(59730, $cues[2]->getStartMs());
        $this->assertSame(60000, $cues[2]->getEndMs());
    }
}