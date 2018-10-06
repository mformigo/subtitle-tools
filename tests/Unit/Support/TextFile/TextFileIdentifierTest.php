<?php

namespace Tests\Unit\Support\TextFile;

use App\Support\TextFile\TextFileIdentifier;
use Tests\TestCase;

class TextFileIdentifierTest extends TestCase
{
    private function assertMime($expectedMime, $filePath)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        $this->assertSame($expectedMime, $mimeType);
    }

    private function assertIsTextFile($filePath)
    {
        $identifier = new TextFileIdentifier();

        $this->assertTrue(
            $identifier->isTextFile($filePath),
            '"'.basename($filePath).'" is not identified as a text file'
        );
    }

    private function assertIsNotTextFile($filePath)
    {
        $identifier = new TextFileIdentifier();

        $this->assertFalse(
            $identifier->isTextFile($filePath),
            '"'.basename($filePath).'" should not be identified as a text file'
        );
    }

    /** @test */
    function it_identifies_simple_text_files()
    {
        $this->assertIsTextFile($this->testFilesStoragePath.'text-file-package/identifying/normal.txt');
    }

    /** @test */
    function it_identifies_empty_files()
    {
        $this->assertIsTextFile($this->testFilesStoragePath.'text-file-package/identifying/empty.txt');
    }

    /** @test */
    function it_identifies_text_files_with_control_characters()
    {
        $files = [
            $this->testFilesStoragePath.'text-file-package/identifying/octet-stream-01.txt',
            $this->testFilesStoragePath.'text-file-package/identifying/octet-stream-02.txt',
            $this->testFilesStoragePath.'text-file-package/identifying/octet-stream-03.txt',
            // Tabs are control characters that should always be allowed
            $this->testFilesStoragePath.'text-file-package/identifying/octet-stream-04-with-tabs.txt',
        ];

        foreach ($files as $filePath) {
            $this->assertMime('application/octet-stream', $filePath);

            $this->assertIsTextFile($filePath);
        }
    }

    /** @test */
    function it_does_not_identify_when_there_are_too_many_control_chars()
    {
        $filePath = $this->testFilesStoragePath.'text-file-package/identifying/dat.txt';

        $this->assertMime('application/octet-stream', $filePath);

        $this->assertIsNotTextFile($filePath);
    }

    /** @test */
    function it_rejects_binary_files()
    {
        $files = [
            $this->testFilesStoragePath.'text-file-package/identifying/gif.txt',
            $this->testFilesStoragePath.'text-file-package/identifying/image.jpg',
        ];

        foreach ($files as $filePath) {
            $this->assertIsNotTextFile($filePath);
        }
    }

    /** @test */
    function it_identifies_xml_files()
    {
        $files = [
            $this->testFilesStoragePath.'text-file-package/identifying/xml.xml',
        ];

        foreach ($files as $filePath) {
            $this->assertIsTextFile($filePath);
        }
    }
}
