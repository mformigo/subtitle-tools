<?php

namespace Tests\Unit\Jobs\FileJobs;

use App\Jobs\FileJobs\ConvertToPlainTextJob;
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
            new ConvertToPlainTextJob($fileGroup, $this->testFilesStoragePath.'text/ass/three-cues.ass')
        );

        $fileJob = $fileGroup->fileJobs()->firstOrFail();

        $this->assertSame('three-cues.ass', $fileJob->original_name);

        $lines = read_lines($fileJob->outputStoredFile->filePath);

        $this->assertCount(7, $lines);

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