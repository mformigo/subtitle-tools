<?php

namespace Tests\Unit;

use App\Subtitles\PlainText\Ass;
use App\Subtitles\PlainText\GenericSubtitle;
use Tests\TestCase;

class AssTest extends TestCase
{
    /** @test */
    function it_loads_from_file()
    {
        $ass = new Ass();

        $ass->loadFile("{$this->testFilesStoragePath}TextFiles/mime-octet-utf8.ass");

        $this->assertSame('mime-octet-utf8', $ass->getFileNameWithoutExtension());

        $this->assertSame("{$this->testFilesStoragePath}TextFiles/mime-octet-utf8.ass", $ass->getFilePath());
    }

    /** @test */
    function it_transforms_to_generic_subtitle()
    {
        $ass = new Ass();

        $ass->loadFile("{$this->testFilesStoragePath}TextFiles/three-cues.ass");

        $genericSub = $ass->toGenericSubtitle();

        $genericCues = $genericSub->getCues();

        $this->assertTrue($genericSub instanceof GenericSubtitle && !$genericSub instanceof Ass);

        $this->assertSame("{$this->testFilesStoragePath}TextFiles/three-cues.ass", $genericSub->getFilePath());

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
}