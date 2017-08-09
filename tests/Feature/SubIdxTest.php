<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\CreatesUploadedFiles;
use Tests\MocksVobSub2Srt;
use Tests\PostsVobSubs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubIdxTest extends TestCase
{
    use DatabaseMigrations;
    use MocksVobSub2Srt;
    use PostsVobSubs;
    use CreatesUploadedFiles;

    /** @test */
    function the_sub_and_idx_file_are_server_side_required()
    {
        $response = $this->post(route('sub-idx-index'));

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'sub' => __('validation.required', ['attribute' => 'sub']),
                'idx' => __('validation.required', ['attribute' => 'idx']),
            ]);
    }

    /** @test */
    function it_rejects_empty_files()
    {
        $response = $this->post(route('sub-idx-index'), [
            'sub' => $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/empty.srt", "empty.sub"),
            'idx' => $this->createUploadedFile("{$this->testFilesStoragePath}TextFiles/empty.srt", "empty.idx"),
        ]);

        $response->assertStatus(302)
            ->assertSessionHasErrors([
                'sub' => __('validation.file_not_empty', ['attribute' => 'sub']),
                'idx' => __('validation.file_not_empty', ['attribute' => 'idx']),
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
            ->assertSessionHasErrors([
                'sub' => __('validation.subidx_invalid_sub_mime', ['attribute' => 'sub']),
                'idx' => __('validation.textfile',                ['attribute' => 'idx']),
            ]);
    }

    /** @test */
    function it_stores_valid_uploads_in_the_database_and_on_the_disk()
    {
        $this->withoutJobs();

        $subIdx = $this->postVobSub();

        $this->assertTrue(file_exists("{$subIdx->filePathWithoutExtension}.sub"), "Stored sub file does not exist");
        $this->assertTrue(file_exists("{$subIdx->filePathWithoutExtension}.idx"), "Stored idx file does not exist");
    }

    /** @test */
    function it_creates_language_extract_jobs()
    {
        $this->expectsJobs(\App\Jobs\ExtractSubIdxLanguage::class);

        $this->postVobSub();

        $this->assertDatabaseHas('sub_idx_languages', ['sub_idx_id' => 1, 'index' => 0, 'language' => 'unknown']);
        $this->assertDatabaseHas('sub_idx_languages', ['sub_idx_id' => 1, 'index' => 1, 'language' => 'nl']);
    }

    /** @test */
    function it_logs_vobsub2srt_output()
    {
        $this->withoutJobs();

        $subIdx = $this->postVobSub();

        $this->assertDatabaseHas('vobsub2srt_outputs', ['sub_idx_id' => 1, 'argument' => '--langlist']);

        $outputs = $subIdx->vobsub2srtOutputs()->firstOrFail();

        $this->assertTrue(strlen($outputs->output) > 20, "Logged output is too short, expecting at least 20 characters");
    }

    /** @test */
    function it_extracts_languages()
    {
        $this->useMockVobSub2Srt();

        $subIdx = $this->postVobSub();

        $languages = $subIdx->languages()
            ->where('has_error', false)
            ->whereNotNull('started_at')
            ->whereNotNull('finished_at')
            ->get();

        $this->assertSame(2, count($languages));

        foreach($languages->all() as $lang) {
            $this->assertTrue(file_exists($lang->filePath), "Extracted file does not exist ({$lang->filePath})");

            $this->assertTrue(filesize($lang->filepath) > 0, "Extracted file is empty");
        }
    }

    /** @test */
    function it_fires_an_event_after_extracting_a_language()
    {
        $this->useMockVobSub2Srt();

        $this->expectsEvents(\App\Events\ExtractingSubIdxLanguageChanged::class);

        $this->postVobSub();
    }
}
