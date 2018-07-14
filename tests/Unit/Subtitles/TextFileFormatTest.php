<?php

namespace Tests\Unit\Subtitles;

use App\Subtitles\PlainText\Ass;
use App\Subtitles\PlainText\MicroDVD;
use App\Subtitles\PlainText\Mpl2;
use App\Subtitles\PlainText\Otranscribe\Otranscribe;
use App\Subtitles\PlainText\PlainText;
use App\Subtitles\PlainText\Smi;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\PlainText\Ssa;
use App\Subtitles\PlainText\WebVtt;
use App\Subtitles\TextFileFormat;
use Tests\TestCase;

class TextFileFormatTest extends TestCase
{
    private function assertTextFileFormat($expectedClass, $filePath, $notThisClass = [])
    {
        $textFileFormat = new TextFileFormat();

        if(! file_exists($filePath)) {
            $this->fail("File does not exist ({$filePath})");
        }

        $fileName = basename($filePath);

        $format = $textFileFormat->getMatchingFormat($filePath, false);

        $this->assertTrue($format instanceof $expectedClass, "'{$fileName}' is not an instance of " . class_basename($expectedClass));

        foreach(array_wrap($notThisClass) as $notThis) {
            $this->assertFalse($format instanceof $notThis, "'{$fileName}' should not be an instanceof " . class_basename($notThis));
        }
    }

    /** @test */
    function it_matches_webvtt_files()
    {
        $this->assertTextFileFormat(WebVtt::class, $this->testFilesStoragePath.'text/vtt/webvtt01.vtt');
        $this->assertTextFileFormat(WebVtt::class, $this->testFilesStoragePath.'text/vtt/webvtt02--with-cue-numbers.vtt');
        $this->assertTextFileFormat(WebVtt::class, $this->testFilesStoragePath.'text/vtt/webvtt-no-dialogue.vtt');
    }

    /** @test */
    function it_matches_srt_files()
    {
        $this->assertTextFileFormat(Srt::class, $this->testFilesStoragePath.'text/srt/normal-01.srt');
        $this->assertTextFileFormat(Srt::class, $this->testFilesStoragePath.'text/srt/coordinates.srt');
        $this->assertTextFileFormat(Srt::class, $this->testFilesStoragePath.'text/srt/spaces-after-colons-and-single-dash-arrow.srt');
        $this->assertTextFileFormat(Srt::class, $this->testFilesStoragePath.'text/srt/dots-in-timings.srt');
    }

    /** @test */
    function it_matches_ass_files()
    {
        $this->assertTextFileFormat(Ass::class, $this->testFilesStoragePath.'text/ass/normal01.ass', Ssa::class);
        $this->assertTextFileFormat(Ass::class, $this->testFilesStoragePath.'text/ass/ass01.ass', Ssa::class);
        $this->assertTextFileFormat(Ass::class, $this->testFilesStoragePath.'text/ass/ass02.ass', Ssa::class);
        $this->assertTextFileFormat(Ass::class, $this->testFilesStoragePath.'text/ass/ass03.ass', Ssa::class);
        $this->assertTextFileFormat(Ass::class, $this->testFilesStoragePath.'text/ass/ass04.ass', Ssa::class);
    }

    /** @test */
    function it_matches_ssa_files()
    {
        $this->assertTextFileFormat(Ssa::class, $this->testFilesStoragePath.'text/ssa/normal01.ssa');
        $this->assertTextFileFormat(Ssa::class, $this->testFilesStoragePath.'text/ssa/ssa01.ssa');
        $this->assertTextFileFormat(Ssa::class, $this->testFilesStoragePath.'text/ssa/ssa02.ssa');
        $this->assertTextFileFormat(Ssa::class, $this->testFilesStoragePath.'text/ssa/ssa03.ssa');
        $this->assertTextFileFormat(Ssa::class, $this->testFilesStoragePath.'text/ssa/ssa04--with-ass-header.ssa');
    }

    /** @test */
    function it_matches_smi_files()
    {
        $this->assertTextFileFormat(Smi::class, $this->testFilesStoragePath.'text/smi/normal01.smi');
        $this->assertTextFileFormat(Smi::class, $this->testFilesStoragePath.'text/smi/smi01.smi');
        $this->assertTextFileFormat(Smi::class, $this->testFilesStoragePath.'text/smi/smi02.smi');
        $this->assertTextFileFormat(Smi::class, $this->testFilesStoragePath.'text/smi/smi03.smi');
        $this->assertTextFileFormat(Smi::class, $this->testFilesStoragePath.'text/smi/smi04.smi');
        $this->assertTextFileFormat(Smi::class, $this->testFilesStoragePath.'text/smi/smi05--no-sami-tag.smi');
        $this->assertTextFileFormat(Smi::class, $this->testFilesStoragePath.'text/smi/smi06--no-sami-tag.smi');
    }

    /** @test */
    function it_matches_microdvd_files()
    {
        $this->assertTextFileFormat(MicroDVD::class, $this->testFilesStoragePath.'text/microdvd/normal01.sub');
        $this->assertTextFileFormat(MicroDVD::class, $this->testFilesStoragePath.'text/microdvd/microdvd01.sub');
        $this->assertTextFileFormat(MicroDVD::class, $this->testFilesStoragePath.'text/microdvd/microdvd02.sub');
    }

    /** @test */
    function it_matches_mpl2_files()
    {
        $this->assertTextFileFormat(Mpl2::class, $this->testFilesStoragePath.'text/mpl2/mpl2-01.mpl');
        $this->assertTextFileFormat(Mpl2::class, $this->testFilesStoragePath.'text/mpl2/mpl2-02.mpl');
        $this->assertTextFileFormat(Mpl2::class, $this->testFilesStoragePath.'text/mpl2/mpl2-03.mpl');
    }

    /** @test */
    function it_matches_otranscribe_files()
    {
        $this->assertTextFileFormat(Otranscribe::class, $this->testFilesStoragePath.'text/otranscribe/otranscribe-01.txt');
    }

    /** @test */
    function it_matches_plain_text_files()
    {
        $textFileFormat = new TextFileFormat();

        $subtitle = $textFileFormat->getMatchingFormat($this->testFilesStoragePath.'text/normal01.txt');

        $this->assertTrue($subtitle instanceof PlainText);
    }

    /** @test */
    function it_matches_empty_files()
    {
        $textFileFormat = new TextFileFormat();

        $subtitle = $textFileFormat->getMatchingFormat($this->testFilesStoragePath.'text/srt/empty.srt');

        $this->assertTrue($subtitle instanceof PlainText);
    }

    /** @test */
    function loading_is_optional()
    {
        $textFileFormat = new TextFileFormat();

        $subtitle = $textFileFormat->getMatchingFormat($this->testFilesStoragePath.'text/srt/normal-01.srt', false);

        $this->assertTrue($subtitle instanceof Srt, "Not an instance of Srt");

        $this->assertTrue($subtitle->getCues() === []);
    }
}