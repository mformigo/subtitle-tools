<?php

namespace Tests\Unit\Subtitles\Tools;

use App\Subtitles\PlainText\Srt;
use App\Subtitles\Tools\Options\SrtCleanerOptions;
use App\Subtitles\Tools\SrtCleaner;
use Tests\TestCase;

class SrtCleanerTest extends TestCase
{
    /** @test */
    function it_can_strip_cues_with_a_music_note()
    {
        $srt = (new Srt)
            ->createCue(100, 200, '♪ take on me ♪')
            ->createCue(300, 400, 'Please do not sing...')
            ->createCue(500, 600, ['Ok', 'Sorry ♪♪♪ boss']);

        $options = (new SrtCleanerOptions)->stripCuesWithMusicNote();

        (new SrtCleaner)->clean($srt, $options);

        $this->assertCount(1, $srt->getCues());

        $this->assertSame(
            'Please do not sing...',
            $srt->getCues()[0]->getLinesAsText()
        );
    }
}
