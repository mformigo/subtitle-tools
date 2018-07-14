<?php

namespace Tests\Unit\Subtitles\PlainText;

use App\Subtitles\PlainText\GenericSubtitle;
use App\Subtitles\PlainText\Mpl2;
use Tests\TestCase;

class Mpl2Test extends TestCase
{
    /** @test */
    function it_loads_from_file()
    {
        $mpl2 = new Mpl2($this->testFilesStoragePath.'text/mpl2/three-cues.mpl');

        $this->assertSame('three-cues', $mpl2->getFileNameWithoutExtension());

        $this->assertSame($this->testFilesStoragePath.'text/mpl2/three-cues.mpl', $mpl2->getFilePath());
    }

    /** @test */
    function it_transforms_to_generic_subtitle()
    {
        $mpl2 = new Mpl2($this->testFilesStoragePath.'text/mpl2/three-cues.mpl');

        $genericSub = $mpl2->toGenericSubtitle();

        $genericCues = $genericSub->getCues();

        $this->assertTrue($genericSub instanceof GenericSubtitle && ! $genericSub instanceof Mpl2);

        $this->assertSame($this->testFilesStoragePath.'text/mpl2/three-cues.mpl', $genericSub->getFilePath());

        $this->assertSame('three-cues', $genericSub->getFileNameWithoutExtension());

        $this->assertCount(3, $genericCues);

        $this->assertSame(300, $genericCues[0]->getStartMs());
        $this->assertSame(2600, $genericCues[0]->getEndMs());
        $this->assertSame(['<i>Poprzednio:</i>'], $genericCues[0]->getLines());

        $this->assertSame(125200, $genericCues[1]->getStartMs());
        $this->assertSame(126300, $genericCues[1]->getEndMs());
        $this->assertSame(['Dutch.'], $genericCues[1]->getLines());

        $this->assertSame(1090600, $genericCues[2]->getStartMs());
        $this->assertSame(1093300, $genericCues[2]->getEndMs());
        $this->assertSame(['- Dlaczego?', '- Nie jestem pewna.'], $genericCues[2]->getLines());
    }
}
