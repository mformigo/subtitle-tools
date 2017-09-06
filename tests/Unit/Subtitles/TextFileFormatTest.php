<?php

namespace Tests\Unit;

use App\Subtitles\PlainText\Ass;
use App\Subtitles\PlainText\MicroDVD;
use App\Subtitles\PlainText\PlainText;
use App\Subtitles\PlainText\Smi;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\PlainText\Ssa;
use App\Subtitles\TextFileFormat;
use Tests\TestCase;

class TextFileFormatTest extends TestCase
{
    /** @test */
    function it_matches_srt_files()
    {
        $textFileFormat = new TextFileFormat();

        $srtFiles = [
            "TextFiles/Normal/normal01.srt",
        ];

        foreach($srtFiles as $fileName) {
            $subtitle = $textFileFormat->getMatchingFormat("{$this->testFilesStoragePath}$fileName");

            $this->assertTrue($subtitle instanceof Srt, "'{$fileName}' is not an instance of Srt");
        }
    }

    /** @test */
    function it_matches_ass_files()
    {
        $textFileFormat = new TextFileFormat();

        $assFiles = [
            "TextFiles/Normal/normal01.ass",
            "TextFiles/FormatDetection/ass01.ass",
            "TextFiles/FormatDetection/ass02.ass",
            "TextFiles/FormatDetection/ass03.ass",
            "TextFiles/FormatDetection/ass04.ass",
        ];

        foreach($assFiles as $fileName) {
            $subtitle = $textFileFormat->getMatchingFormat("{$this->testFilesStoragePath}$fileName");

            $this->assertTrue($subtitle instanceof Ass, "'{$fileName}' is not an instance of Ass");

            $this->assertFalse($subtitle instanceof Ssa, "'{$fileName}' was Ssa, should be Ass");
        }
    }

    /** @test */
    function it_matches_ssa_files()
    {
        $textFileFormat = new TextFileFormat();

        $assFiles = [
            "TextFiles/Normal/normal01.ssa",
            "TextFiles/FormatDetection/ssa01.ssa",
            "TextFiles/FormatDetection/ssa02.ssa",
            "TextFiles/FormatDetection/ssa03.ssa",
        ];

        foreach($assFiles as $fileName) {
            $subtitle = $textFileFormat->getMatchingFormat("{$this->testFilesStoragePath}$fileName");

            $this->assertTrue($subtitle instanceof Ssa, "'{$fileName}' is not an instance of Ssa");
        }
    }

    /** @test */
    function it_matches_smi_files()
    {
        $textFileFormat = new TextFileFormat();

        $smiFiles = [
            "TextFiles/Normal/normal01.smi",
            "TextFiles/FormatDetection/smi01.smi",
            "TextFiles/FormatDetection/smi02.smi",
            "TextFiles/FormatDetection/smi03.smi",
            "TextFiles/FormatDetection/smi04.smi",
        ];

        foreach($smiFiles as $fileName) {
            $subtitle = $textFileFormat->getMatchingFormat("{$this->testFilesStoragePath}$fileName");

            $this->assertTrue($subtitle instanceof Smi, "'{$fileName}' is not an instance of Smi");
        }
    }

    /** @test */
    function it_matches_microdvd_files()
    {
        $textFileFormat = new TextFileFormat();

        $MicroDVDFiles = [
            'TextFiles/Normal/normal01.sub',
            'TextFiles/FormatDetection/microdvd01.sub',
            'TextFiles/FormatDetection/microdvd02.sub',
        ];

        foreach($MicroDVDFiles as $fileName) {
            $subtitle = $textFileFormat->getMatchingFormat("{$this->testFilesStoragePath}$fileName");

            $this->assertTrue($subtitle instanceof MicroDVD, "'{$fileName}' is not an instance of MicroDVD");
        }
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