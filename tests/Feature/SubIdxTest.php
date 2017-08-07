<?php

namespace Tests\Feature;

use App\Models\SubIdx;
use App\Subtitles\VobSub\VobSub2SrtInterface;
use App\Subtitles\VobSub\VobSub2SrtMock;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubIdxTest extends TestCase
{
    use DatabaseMigrations;

    private $defaultSubIdxName = "error-and-nl";

    private function useMockVobSub2Srt()
    {
        $this->app->bind(VobSub2SrtInterface::class, function($app, $args) {
            return new VobSub2SrtMock(
                $args['path'],
                $args['subIdx'] ?? null
            );
        });
    }

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
            ->assertSessionHasErrors([
                'sub' => __('validation.subidx_invalid_sub_mime', ['attribute' => 'sub']),
                'idx' => __('validation.textfile',                ['attribute' => 'idx']),
            ]);
    }

    /** @test */
    function it_stores_valid_uploads_in_the_database_and_on_the_disk()
    {
        $this->withoutJobs();

        $response = $this->post(route('sub-idx-index'), $this->getSubIdxPostData());

        $subIdx = SubIdx::where('original_name', $this->defaultSubIdxName)->firstOrFail();

        $this->assertTrue(file_exists("{$subIdx->filePathWithoutExtension}.sub"));
        $this->assertTrue(file_exists("{$subIdx->filePathWithoutExtension}.idx"));

        $response->assertStatus(302)
            ->assertRedirect(route('sub-idx-detail', ['pageId' => $subIdx->page_id]));
    }

    /** @test */
    function it_creates_language_extract_jobs()
    {
        $this->expectsJobs(\App\Jobs\ExtractSubIdxLanguage::class);

        $response = $this->post(route('sub-idx-index'), $this->getSubIdxPostData());

        $this->assertDatabaseHas('sub_idx_languages', ['sub_idx_id' => 1, 'index' => 0, 'language' => 'unknown']);
        $this->assertDatabaseHas('sub_idx_languages', ['sub_idx_id' => 1, 'index' => 1, 'language' => 'nl']);
    }

    /** @test */
    function it_logs_vobsub2srt_output()
    {
        $this->withoutJobs();

        $response = $this->post(route('sub-idx-index'), $this->getSubIdxPostData());

        $this->assertDatabaseHas('vobsub2srt_outputs', ['sub_idx_id' => 1, 'argument' => '--langlist']);

        $outputs = SubIdx::findOrFail(1)->vobsub2srtOutputs()->firstOrFail();

        $this->assertTrue(strlen($outputs->output) > 20, "Logged output is too short, we expect at least 20 characters");
    }

    /** @test */
    function it_extracts_languages()
    {
        $this->useMockVobSub2Srt();

        $response = $this->post(route('sub-idx-index'), $this->getSubIdxPostData());

        $subIdx = SubIdx::where('original_name', $this->defaultSubIdxName)->firstOrFail();

        $response->assertStatus(302)
            ->assertRedirect(route('sub-idx-detail', ['pageId' => $subIdx->page_id]));

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

        $this->expectsEvents(\App\Events\ExtractedSubIdxLanguage::class);

        $response = $this->post(route('sub-idx-index'), $this->getSubIdxPostData());
    }

}
