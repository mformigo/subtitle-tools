<?php

namespace Tests\Unit\Jobs;

use App\Jobs\FileJobs\ConvertToSrtJob;
use App\Jobs\ZipFileGroupJob;
use App\Models\FileGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ZipFileGroupJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_zips_files_from_a_file_group()
    {
        $fileGroup = $this->createFileGroup();

        dispatch(
            new ConvertToSrtJob($fileGroup, $this->testFilesStoragePath.'text/ass/three-cues.ass')
        );

        $fileGroup = FileGroup::findOrFail(1);

        $this->assertNotNull($fileGroup->file_jobs_finished_at);

        dispatch(
            new ZipFileGroupJob($fileGroup)
        );

        $fileGroup = FileGroup::findOrFail(1);

        $this->assertNotNull($fileGroup->archive_finished_at);
        $this->assertNotNull($fileGroup->archive_stored_file_id);
    }

    /**
     * @param string $toolRoute
     * @param null $urlKey
     *
     * @return FileGroup
     *
     * @deprecated This is old, should be replaced by "createFileGroup" method from the "CreatesModels" trait
     */
    public function createFileGroup($toolRoute = 'default-route', $urlKey = null): FileGroup
    {
        $fileGroup = new FileGroup();

        $fileGroup->fill([
            'tool_route' => $toolRoute,
            'url_key' => $urlKey ?? generate_url_key(),
        ])->save();

        return $fileGroup;
    }
}