<?php

namespace Tests\Unit;

use App\Subtitles\PlainText\GenericSubtitle;
use App\Subtitles\PlainText\Smi;
use Tests\TestCase;

class SmiTest extends TestCase
{
    /** @test */
    function it_loads_from_file()
    {
        $smi = new Smi();

        $smi->loadFile("{$this->testFilesStoragePath}TextFiles/Normal/normal01.smi");

        $this->assertSame('normal01', $smi->getFileNameWithoutExtension());

        $this->assertSame("{$this->testFilesStoragePath}TextFiles/Normal/normal01.smi", $smi->getFilePath());
    }

    /** @test */
    function it_transforms_to_generic_subtitle()
    {
        $smi = new Smi();

        $smi->loadFile("{$this->testFilesStoragePath}TextFiles/three-cues.smi");

        $genericSub = $smi->toGenericSubtitle();

        $this->assertTrue($genericSub instanceof GenericSubtitle);

        $this->assertFalse($genericSub instanceof Smi);

        $this->assertSame("{$this->testFilesStoragePath}TextFiles/three-cues.smi", $genericSub->getFilePath());

        $this->assertSame("three-cues", $genericSub->getFileNameWithoutExtension());

        $genericCues = $genericSub->getCues();

        $this->assertSame(3, count($genericCues));

        $this->assertSame(69528, $genericCues[0]->getStartMs());
        $this->assertSame(73156, $genericCues[0]->getEndMs());
        $this->assertSame(['가자, 가자'], $genericCues[0]->getLines());

        $this->assertSame(73323, $genericCues[1]->getStartMs());
        $this->assertSame(75491, $genericCues[1]->getEndMs());
        $this->assertSame(['안녕하십니까, 숙녀분들'], $genericCues[1]->getLines());

        $this->assertSame(75617, $genericCues[2]->getStartMs());
        $this->assertSame(77076, $genericCues[2]->getEndMs());
        $this->assertSame(['지갑 좀', '보여주시겠습니까?'], $genericCues[2]->getLines());
    }
}