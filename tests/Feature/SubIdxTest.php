<?php

namespace Tests\Feature;

use App\Models\SubIdx;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SubIdxTest extends TestCase
{
    use DatabaseMigrations;

    private $defaultSubIdxName = "error-and-nl";

    private function getSubUploadedFile($fileNameWithoutExtension = null)
    {
        $fileNameWithoutExtension = $fileNameWithoutExtension ?: $this->defaultSubIdxName;

        return new UploadedFile(
            $this->testFilesStoragePath . "SubIdxFiles/{$fileNameWithoutExtension}.sub",
            "{$fileNameWithoutExtension}.sub",
            null, null, null, true
        );
    }

    private function getIdxUploadedFile($fileNameWithoutExtension = null)
    {
        $fileNameWithoutExtension = $fileNameWithoutExtension ?: $this->defaultSubIdxName;

        return new UploadedFile(
            $this->testFilesStoragePath . "SubIdxFiles/{$fileNameWithoutExtension}.idx",
            "{$fileNameWithoutExtension}.idx",
            null, null, null, true
        );
    }

    private function getSubIdxPostData($fileNameWithoutExtension = null)
    {
        return [
            'sub' => $this->getSubUploadedFile($fileNameWithoutExtension),
            'idx' => $this->getIdxUploadedFile($fileNameWithoutExtension),
        ];
    }

    /** @test */
    function the_sub_and_idx_file_are_server_side_required()
    {
        $response = $this->post(route('sub-idx-index'));

        $response->assertStatus(302)
            ->assertRedirect(route('sub-idx-index'))
            ->assertSessionHasErrors([
                'sub' => __('validation.required', ['attribute' => 'sub']),
                'idx' => __('validation.required', ['attribute' => 'idx']),
            ]);
    }

    /** @test */
    function it_validates_uploaded_files()
    {
        $response = $this->post(route('sub-idx-index'), [
            'sub' => UploadedFile::fake()->image('movie.sub'),
            'idx' => UploadedFile::fake()->image('text.idx'),
        ]);

        $response->assertStatus(302)
            ->assertRedirect(route('sub-idx-index'))
            ->assertSessionHasErrors([
                'sub' => __('validation.subidx_invalid_sub_mime', ['attribute' => 'sub']),
                'idx' => __('validation.textfile',                ['attribute' => 'idx']),
            ]);
    }

    /** @test */
    function it_stores_valid_uploads_in_the_database_and_on_the_disk()
    {
        $response = $this->post(route('sub-idx-index'), $this->getSubIdxPostData());

        $subIdx = SubIdx::where(['original_name' => $this->defaultSubIdxName])->firstOrFail();

        $this->assertTrue(file_exists("{$subIdx->filePathWithoutExtension}.sub"));
        $this->assertTrue(file_exists("{$subIdx->filePathWithoutExtension}.idx"));

        $response->assertStatus(302)
            ->assertRedirect(route('sub-idx-detail', ['pageId' => $subIdx->page_id]));
    }

    /** @test */
    function it_creates_language_extract_jobs()
    {
        $response = $this->post(route('sub-idx-index'), $this->getSubIdxPostData());

        $this->assertDatabaseHas('sub_idx_languages', ['sub_idx_id' => 1, 'index' => 0, 'language' => 'unknown']);
        $this->assertDatabaseHas('sub_idx_languages', ['sub_idx_id' => 1, 'index' => 1, 'language' => 'nl']);

        $this->assertDatabaseHas('jobs', ['id' => 1, 'queue' => 'sub-idx']);
        $this->assertDatabaseHas('jobs', ['id' => 2, 'queue' => 'sub-idx']);
    }

}
