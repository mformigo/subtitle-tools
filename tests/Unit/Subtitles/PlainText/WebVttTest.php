<?php

namespace Tests\Unit\Subtitles\PlainText;

use App\Support\TextFile\Facades\TextFileReader;
use App\Subtitles\PartialShiftsCues;
use App\Subtitles\PlainText\WebVtt;
use App\Subtitles\PlainText\WebVttCue;
use App\Subtitles\ShiftsCues;
use Tests\TestCase;

class WebVttTest extends TestCase
{
    /** @test */
    function it_preserves_valid_webvtt_files()
    {
        $filePath = $this->testFilesStoragePath.'text/vtt/normal01.vtt';

        $webVtt = new WebVtt($filePath);

        $content = TextFileReader::getLines($filePath);

        $this->assertSame($content, $webVtt->getContentLines());
    }

    /** @test */
    function it_shifts_cues()
    {
        $webVtt = new WebVtt($this->testFilesStoragePath.'text/vtt/three-cues.vtt');

        $this->assertTrue($webVtt instanceof ShiftsCues);

        $lines = $webVtt->getContentLines();
        $this->assertSame('00:00:05.160 --> 00:00:10.100', $lines[2]);
        $this->assertSame('00:00:10.100 --> 00:00:15.600 position:10%,line-left align:left size:35%', $lines[5]);
        $this->assertSame('00:00:15.600 --> 00:00:19.270', $lines[8]);

        $webVtt->shift(1000);

        $lines = $webVtt->getContentLines();
        $this->assertSame('00:00:06.160 --> 00:00:11.100', $lines[2]);
        $this->assertSame('00:00:11.100 --> 00:00:16.600 position:10%,line-left align:left size:35%', $lines[5]);
        $this->assertSame('00:00:16.600 --> 00:00:20.270', $lines[8]);

        $webVtt->shift('-1000');

        $lines = $webVtt->getContentLines();
        $this->assertSame('00:00:05.160 --> 00:00:10.100', $lines[2]);
        $this->assertSame('00:00:10.100 --> 00:00:15.600 position:10%,line-left align:left size:35%', $lines[5]);
        $this->assertSame('00:00:15.600 --> 00:00:19.270', $lines[8]);
    }

    /** @test */
    function it_partial_shifts_cues()
    {
        $webVtt = new WebVtt($this->testFilesStoragePath.'text/vtt/three-cues.vtt');

        $this->assertTrue($webVtt instanceof PartialShiftsCues);

        $lines = $webVtt->getContentLines();
        $this->assertSame('00:00:05.160 --> 00:00:10.100', $lines[2]);
        $this->assertSame('00:00:10.100 --> 00:00:15.600 position:10%,line-left align:left size:35%', $lines[5]);
        $this->assertSame('00:00:15.600 --> 00:00:19.270', $lines[8]);

        $webVtt->shiftPartial(12000, 9999999, 1000);

        $lines = $webVtt->getContentLines();
        $this->assertSame('00:00:05.160 --> 00:00:10.100', $lines[2]);
        $this->assertSame('00:00:10.100 --> 00:00:15.600 position:10%,line-left align:left size:35%', $lines[5]);
        $this->assertSame('00:00:16.600 --> 00:00:20.270', $lines[8]);

        $webVtt->shiftPartial(0, 16000, '-1000');

        $lines = $webVtt->getContentLines();
        $this->assertSame('00:00:04.160 --> 00:00:09.100', $lines[2]);
        $this->assertSame('00:00:09.100 --> 00:00:14.600 position:10%,line-left align:left size:35%', $lines[5]);
        $this->assertSame('00:00:16.600 --> 00:00:20.270', $lines[8]);
    }

    /** @test */
    function it_transforms_to_generic_subtitle()
    {
        $webVtt = new WebVtt($this->testFilesStoragePath.'text/vtt/three-cues.vtt');

        $generic = $webVtt->toGenericSubtitle();

        $this->assertFalse($generic instanceof WebVtt);

        $this->assertSame('three-cues', $generic->getFileNameWithoutExtension());

        $cues = $generic->getCues();

        $this->assertSame(3, count($cues));

        $this->assertSame(5160, $cues[0]->getStartMs());
        $this->assertSame(10100, $cues[0]->getEndMs());
        $this->assertSame(['Hello everyone and welcome to an introduction to linear regression and this lecture we\'re going to get'], $cues[0]->getLines());
        $this->assertFalse($cues[0] instanceof WebVttCue);

        $this->assertSame(10100, $cues[1]->getStartMs());
        $this->assertSame(15600, $cues[1]->getEndMs());
        $this->assertSame(['a light theoretical background history behind the idea of linear regression before we actually tackle'], $cues[1]->getLines());
        $this->assertFalse($cues[1] instanceof WebVttCue);

        $this->assertSame(15600, $cues[2]->getStartMs());
        $this->assertSame(19270, $cues[2]->getEndMs());
        $this->assertSame(['the concept with Python and the sikat learn library.', 'This is the second line!!'], $cues[2]->getLines());
        $this->assertFalse($cues[2] instanceof WebVttCue);
    }

    /** @test */
    function it_can_parse_a_file_that_ends_with_a_cue_without_dialogue()
    {
        $webVtt = new WebVtt($this->testFilesStoragePath.'text/vtt/webvtt-edge-case-01.vtt');

        $generic = $webVtt->toGenericSubtitle();

        $cues = $generic->getCues();

        $this->assertSame(2, count($cues));

        $this->assertSame(48400, $cues[0]->getStartMs());
        $this->assertSame(52000, $cues[0]->getEndMs());
        $this->assertSame(['小布希总统宣佈胜利'], $cues[0]->getLines());
        $this->assertFalse($cues[0] instanceof WebVttCue);

        $this->assertSame(54000, $cues[1]->getStartMs());
        $this->assertSame(57200, $cues[1]->getEndMs());
        $this->assertSame(['Last cue has no dialogue'], $cues[1]->getLines());
        $this->assertFalse($cues[1] instanceof WebVttCue);
    }

    /** @test */
    function it_can_parse_a_file_with_no_dialogue()
    {
        $webVtt = new WebVtt($this->testFilesStoragePath.'text/vtt/webvtt-no-dialogue.vtt');

        $generic = $webVtt->toGenericSubtitle();

        $cues = $generic->getCues();

        $this->assertSame(0, count($cues));
    }
}