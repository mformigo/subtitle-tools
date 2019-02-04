<?php

namespace Tests\Unit\Controllers;

use App\Models\FileGroup;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FileGroupArchiveControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_download_a_file_group_archive()
    {
        $archive = $this->createStoredFile();

        $fileGroup = $this->createFileGroup(['archive_stored_file_id' => $archive->id]);

        $this->postDownload($fileGroup)
            ->assertStatus(200)
            ->assertHeader('content-disposition', 'attachment; filename=subtitletools-archive.zip');
    }

    /** @test */
    function it_throws_a_404_when_no_archive_exists()
    {
        $fileGroup = $this->createFileGroup(['archive_stored_file_id' => null]);

        $this->postDownload($fileGroup)->assertStatus(404);
    }

    /** @test */
    function it_redirects_get_method_downloads_to_the_result_page()
    {
        $fileGroup = $this->createFileGroup();

        $this->getDownload($fileGroup)
            ->assertRedirect($fileGroup->result_route);
    }

    private function postDownload($urlKey)
    {
        if ($urlKey instanceof FileGroup) {
            $urlKey = $urlKey->url_key;
        }

        return $this->post(route('fileGroup.archive.download', $urlKey));
    }

    private function getDownload($urlKey)
    {
        if ($urlKey instanceof FileGroup) {
            $urlKey = $urlKey->url_key;
        }

        return $this->get(route('fileGroup.archive.downloadRedirect', $urlKey));
    }
}
