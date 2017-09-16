<?php

namespace Tests\Unit;

use App\Subtitles\PlainText\Ass;
use App\Subtitles\PlainText\MicroDVD;
use App\Subtitles\PlainText\Mpl2;
use App\Subtitles\PlainText\PlainText;
use App\Subtitles\PlainText\Smi;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\PlainText\Ssa;
use App\Subtitles\TextFileFormat;
use Tests\TestCase;

class TextFileFormatTest extends TestCase
{
    private function assertTextFileFormat($expectedClass, $filePath, $notThisClass = [])
    {
        $textFileFormat = new TextFileFormat();

        if(!file_exists($filePath)) {
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
    function it_matches_srt_files()
    {
        $this->assertTextFileFormat(Srt::class, "{$this->testFilesStoragePath}TextFiles/Normal/normal01.srt");
    }

    /** @test */
    function it_matches_ass_files()
    {
        $this->assertTextFileFormat(Ass::class, "{$this->testFilesStoragePath}TextFiles/Normal/normal01.ass", Ssa::class);
        $this->assertTextFileFormat(Ass::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/ass01.ass", Ssa::class);
        $this->assertTextFileFormat(Ass::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/ass02.ass", Ssa::class);
        $this->assertTextFileFormat(Ass::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/ass03.ass", Ssa::class);
        $this->assertTextFileFormat(Ass::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/ass04.ass", Ssa::class);
    }

    /** @test */
    function it_matches_ssa_files()
    {
        $this->assertTextFileFormat(Ssa::class, "{$this->testFilesStoragePath}TextFiles/Normal/normal01.ssa");
        $this->assertTextFileFormat(Ssa::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/ssa01.ssa");
        $this->assertTextFileFormat(Ssa::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/ssa02.ssa");
        $this->assertTextFileFormat(Ssa::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/ssa03.ssa");
    }

    /** @test */
    function it_matches_smi_files()
    {
        $this->assertTextFileFormat(Smi::class, "{$this->testFilesStoragePath}TextFiles/Normal/normal01.smi");
        $this->assertTextFileFormat(Smi::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/smi01.smi");
        $this->assertTextFileFormat(Smi::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/smi02.smi");
        $this->assertTextFileFormat(Smi::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/smi03.smi");
        $this->assertTextFileFormat(Smi::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/smi04.smi");
        $this->assertTextFileFormat(Smi::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/smi05--no-sami-tag.smi");
    }

    /** @test */
    function it_matches_microdvd_files()
    {
        $this->assertTextFileFormat(MicroDVD::class, "{$this->testFilesStoragePath}TextFiles/Normal/normal01.sub");
        $this->assertTextFileFormat(MicroDVD::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/microdvd01.sub");
        $this->assertTextFileFormat(MicroDVD::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/microdvd02.sub");
    }

    /** @test */
    function it_matches_mpl2_files()
    {
        $this->assertTextFileFormat(Mpl2::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/mpl2-01.mpl");
        $this->assertTextFileFormat(Mpl2::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/mpl2-02.mpl");
        $this->assertTextFileFormat(Mpl2::class, "{$this->testFilesStoragePath}TextFiles/FormatDetection/mpl2-03.mpl");
    }

    /** @test */
    function it_matches_plain_text_files()
    {
        $textFileFormat = new TextFileFormat();

        $subtitle = $textFileFormat->getMatchingFormat("{$this->testFilesStoragePath}TextFiles/Normal/normal01.txt");

        $this->assertTrue($subtitle instanceof PlainText);
    }

    /** @test */
    function it_matches_empty_files()
    {
        $textFileFormat = new TextFileFormat();

        $subtitle = $textFileFormat->getMatchingFormat("{$this->testFilesStoragePath}TextFiles/empty.srt");

        $this->assertTrue($subtitle instanceof PlainText);
    }

    /** @test */
    function loading_is_optional()
    {
        $textFileFormat = new TextFileFormat();

        $subtitle = $textFileFormat->getMatchingFormat("{$this->testFilesStoragePath}TextFiles/Normal/normal01.srt", false);

        $this->assertTrue($subtitle instanceof Srt, "Not an instance of Srt");

        $this->assertTrue($subtitle->getCues() === []);
    }
}