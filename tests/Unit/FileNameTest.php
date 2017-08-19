<?php

namespace Tests\Unit;

use App\Utils\FileName;
use Tests\TestCase;

class FileNameTest extends TestCase
{
    /** @test */
    function it_gets_extensions()
    {
        $fileName = new FileName();

        $this->assertSame('srt', $fileName->getExtension('file.srt'));

        $this->assertSame('问题', $fileName->getExtension('/home/还没问你问题.问题'));

        $this->assertSame('SRT', $fileName->getExtension('/home/file.SRT', false));

        $this->assertSame('srt', $fileName->getExtension('/home/file.test.srt'));

        $this->assertSame('', $fileName->getExtension('/home/file'));

        $this->assertSame('htaccess', $fileName->getExtension('/home/.htaccess'));
    }

    /** @test */
    function it_changes_extensions()
    {
        $fileName = new FileName();

        $this->assertSame('file.ass', $fileName->changeExtension('file.srt', 'ass'));

        $this->assertSame('/home/file.ass', $fileName->changeExtension('/home/file.srt', 'ass'));

        $this->assertSame('/home/file.test.ass', $fileName->changeExtension('/home/file.test.srt', 'ass'));

        $this->assertSame('/home/file.ass', $fileName->changeExtension('/home/file', 'ass'));
    }

    /** @test */
    function it_gets_names_without_extension()
    {
        $fileName = new FileName();

        $this->assertSame('file', $fileName->getWithoutExtension('file.srt'));

        $this->assertSame('/home/file', $fileName->getWithoutExtension('/home/file'));

        $this->assertSame('/home/还没问你问题', $fileName->getWithoutExtension('/home/还没问你问题.问题'));

        $this->assertSame('/home/file', $fileName->getWithoutExtension('/home/file.SRT'));
    }

    /** @test */
    function it_appends_to_names()
    {
        $fileName = new FileName();

        $this->assertSame('file-st.srt', $fileName->appendName('file.srt', '-st'));

        $this->assertSame('file-st', $fileName->appendName('file', '-st'));
    }

    /** @test */
    function it_watermarks_file_names()
    {
        $fileName = new FileName();

        $this->assertTrue(stripos($fileName->watermark('file.srt'), 'subtitletools.com') !== false);

        $alreadyWatermarked = '/home/file [subtitletools.com].srt';

        $this->assertSame(strlen($alreadyWatermarked), strlen($fileName->watermark($alreadyWatermarked)));
    }
}
