<?php

namespace Tests\Unit;

use SjorsO\TextFile\Facades\TextFileReader;
use App\Jobs\ConvertToPlainTextJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesFileGroups;
use Tests\TestCase;

class ConvertToPlainTextJobTest extends TestCase
{
    use RefreshDatabase, CreatesFileGroups;

    /** @test */
    function it_converts_a_file_to_plain_text()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToPlainTextJob($fileGroup, "{$this->testFilesStoragePath}TextFiles/three-cues.ass")
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('three-cues.ass', $fileJob->original_name);

        $lines = TextFileReader::getLines($fileJob->outputStoredFile->filePath);

        $this->assertSame(7, count($lines));

        $this->assertSame([
            'This is the first line, it is crazy',
            '',
            'Second line starts here',
            'Also crazy',
            '',
            'And this is the third line',
            '',
        ], $lines);
    }
}