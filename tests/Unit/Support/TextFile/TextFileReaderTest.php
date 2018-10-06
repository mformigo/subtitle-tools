<?php

namespace Tests\Unit\Support\TextFile;

use App\Support\TextFile\TextFileReader;
use Tests\TestCase;

class TextFileReaderTest extends TestCase
{
    /** @test */
    function it_reads_content_from_text_files()
    {
        $reader = new TextFileReader();

        $content = $reader->getContent("{$this->testFilesStoragePath}text-file-package/reading/normal.txt");

        $this->assertSame("To be out. This is out.\n[AUDIENCE LAUGHS]\n\n", $content);
    }

    /** @test */
    function it_reads_lines_from_text_files()
    {
        $reader = new TextFileReader();

        $lines = $reader->getLines("{$this->testFilesStoragePath}text-file-package/reading/normal.txt");

        $this->assertSame([
            'To be out. This is out.',
            '[AUDIENCE LAUGHS]',
            '',
            '',
        ], $lines);
    }

    /** @test */
    function it_reads_lines_from_empty_files()
    {
        $reader = new TextFileReader();

        $lines = $reader->getLines("{$this->testFilesStoragePath}text-file-package/reading/empty.txt");

        $this->assertSame([''], $lines);
    }

    /** @test */
    function it_reads_text_files_with_control_characters()
    {
        $reader = new TextFileReader();

        $content = $reader->getContent("{$this->testFilesStoragePath}text-file-package/reading/control-chars.txt");

        $this->assertSame(
            "告訴我努的號碼科委司馬南本月科委司馬南\r\n".
            "\0談話三科家阿科委司馬南本月科委司馬\r\n".
            "力科委司馬南本月科委司馬南本月科委司馬\r\n".
            "力科委司馬南本月力科委司馬南本月力科委\r\n".
            "司馬南本月力科委司馬南本月力科委司馬南\r\n".
            "本月力科委司馬南本月力科委司馬南本月力\r\n".
            '科委司馬南本月力科委司馬南本月力科委司',
        $content);
    }
}
