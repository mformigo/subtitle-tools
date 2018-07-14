<?php

namespace Tests\Unit\Subtitles\PlainText\Otranscribe;

use App\Subtitles\PlainText\GenericSubtitle;
use App\Subtitles\PlainText\GenericSubtitleCue;
use App\Subtitles\PlainText\Otranscribe\Otranscribe;
use Tests\TestCase;

class OtranscribeTest extends TestCase
{
    /** @test */
    function it_loads_from_file()
    {
        $filePath = $this->testFilesStoragePath.'text/otranscribe/otranscribe-01.txt';

        $oTranscribe = new Otranscribe($filePath);

        $this->assertSame('otranscribe-01', $oTranscribe->getFileNameWithoutExtension());

        $this->assertSame($filePath, $oTranscribe->getFilePath());
    }

    /** @test */
    function it_transforms_to_generic_subtitle()
    {
        $filePath = $this->testFilesStoragePath.'text/otranscribe/otranscribe-01.txt';

        $oTranscribe = new Otranscribe($filePath);

        $genericSub = $oTranscribe->toGenericSubtitle();

        $genericCues = $genericSub->getCues();

        $this->assertTrue($genericSub instanceof GenericSubtitle && ! $genericSub instanceof Otranscribe);

        $this->assertSame($filePath, $genericSub->getFilePath());

        $this->assertSame('otranscribe-01', $genericSub->getFileNameWithoutExtension());

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

    /** @test */
    function it_sorts_cues_before_transforming_to_generic_subtitle()
    {
        // Unsorted cues cause "start_ms < end_ms" errors because oTranscribe are closed captions.

        $oTranscribe = new Otranscribe($this->testFilesStoragePath.'text/otranscribe/otranscribe-02-unsorted-cues.txt');

        $genericSubtitleArray = array_map(function (GenericSubtitleCue $cue) {
            return (string) $cue;
        }, $oTranscribe->toGenericSubtitle()->getCues());

        $this->assertSame([
            '{3000}{4000}["wow"]',
            '{4000}{7000}["your side"]',
            '{7000}{15000}["bla bla bla"]',
            '{75000}{79000}["aaa"]',
        ], $genericSubtitleArray);
    }
}
