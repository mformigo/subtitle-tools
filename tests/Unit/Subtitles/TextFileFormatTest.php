<?php

namespace Tests\Unit;

use App\Subtitles\PlainText\Ass;
use App\Subtitles\PlainText\PlainText;
use App\Subtitles\PlainText\Srt;
use App\Subtitles\TextFileFormat;
use Tests\TestCase;

class TextFileFormatTest extends TestCase
{
    /** @test */
    function it_matches_srt_files()
    {
        $textFileFormat = new TextFileFormat();

        $subtitle = $textFileFormat->getMatchingFormat("{$this->testFilesStoragePath}TextFiles/Normal/normal01.srt");

        $this->assertTrue($subtitle instanceof Srt);
    }

    /** @test */
    function it_matches_ass_files()
    {
        $textFileFormat = new TextFileFormat();

        $subtitle = $textFileFormat->getMatchingFormat("{$this->testFilesStoragePath}TextFiles/Normal/normal01.ass");

        $this->assertTrue($subtitle instanceof Ass);
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
}