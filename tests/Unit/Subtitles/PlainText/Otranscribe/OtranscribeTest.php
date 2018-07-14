<?php

namespace Tests\Unit\Subtitles\PlainText\Otranscribe;

use App\Subtitles\PlainText\GenericSubtitle;
use App\Subtitles\PlainText\Otranscribe\Otranscribe;
use Tests\TestCase;

class OtranscribeTest extends TestCase
{
    /** @test */
    function it_loads_from_file()
    {
        $filePath = $this->testFilesStoragePath.'TextFiles/otranscribe/otranscribe-1.txt';

        $oTranscribe = new Otranscribe($filePath);

        $this->assertSame('otranscribe-1', $oTranscribe->getFileNameWithoutExtension());

        $this->assertSame($filePath, $oTranscribe->getFilePath());
    }

    /** @test */
    function it_transforms_to_generic_subtitle()
    {
        $filePath = $this->testFilesStoragePath.'TextFiles/otranscribe/otranscribe-1.txt';

        $oTranscribe = new Otranscribe($filePath);

        $genericSub = $oTranscribe->toGenericSubtitle();

        $genericCues = $genericSub->getCues();

        $this->assertTrue($genericSub instanceof GenericSubtitle && ! $genericSub instanceof Otranscribe);

        $this->assertSame($filePath, $genericSub->getFilePath());

        $this->assertSame('otranscribe-1', $genericSub->getFileNameWithoutExtension());

        $this->assertCount(4, $genericCues);

        $this->assertSame(3000, $genericCues[0]->getStartMs());
        $this->assertSame(4000, $genericCues[0]->getEndMs());
        $this->assertSame(['wow'], $genericCues[0]->getLines());

        $this->assertSame(4000, $genericCues[1]->getStartMs());
        $this->assertSame(7000, $genericCues[1]->getEndMs());
        $this->assertSame(['your side'], $genericCues[1]->getLines());

        $this->assertSame(7000, $genericCues[2]->getStartMs());
        $this->assertSame(15000, $genericCues[2]->getEndMs());
        $this->assertSame(['bla bla bla'], $genericCues[2]->getLines());

        $this->assertSame(75000, $genericCues[3]->getStartMs());
        $this->assertSame(79000, $genericCues[3]->getEndMs());
        $this->assertSame(['aaa'], $genericCues[3]->getLines());
    }
}
