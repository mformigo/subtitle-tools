<?php

namespace Tests\Unit\Controllers;

use App\Models\FileGroup;
use Tests\PostsFileJobs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShiftPartialControllerTest extends TestCase
{
    use RefreshDatabase, PostsFileJobs;

    /** @test */
    function the_fields_are_server_side_required()
    {
        $this->post(route('shiftPartial'))
            ->assertStatus(302)
            ->assertSessionHasErrors([
                'subtitles' => __('validation.required', ['attribute' => 'subtitles']),
                'shifts'    => __('validation.required', ['attribute' => 'shifts']),
            ]);
    }

    /** @test */
    function it_validates_the_shifts_milliseconds_field()
    {
        $this->post(route('shiftPartial'), [
            'shifts' => [['milliseconds' => 'not a number']],
        ])->assertStatus(302)->assertSessionHasErrors(['shifts.0.milliseconds' => __('validation.numeric', ['attribute' => 'shifts.0.milliseconds'])]);

        $this->post(route('shiftPartial'), [
            'shifts' => [['milliseconds' => '']],
        ])->assertStatus(302)->assertSessionHasErrors(['shifts.0.milliseconds' => __('validation.required', ['attribute' => 'shifts.0.milliseconds'])]);

        $this->post(route('shiftPartial'), [
            'shifts' => [['milliseconds' => '1e2']],
        ])->assertStatus(302)->assertSessionHasErrors(['shifts.0.milliseconds' => __('validation.regex', ['attribute' => 'shifts.0.milliseconds'])]);
    }

    /** @test */
    function milliseconds_can_not_be_zero()
    {
        $this->post(route('shiftPartial'), [
            'shifts' => [['milliseconds' => 0]],
        ])->assertStatus(302)->assertSessionHasErrors(['shifts.0.milliseconds' => __('validation.not_in', ['attribute' => 'shifts.0.milliseconds'])]);
    }

    /** @test */
    function it_shows_errors_on_same_page_if_single_file_cant_be_partially_shifted()
    {
        $this->post(route('shiftPartial'), [
            'subtitles' => [$this->createUploadedFile("{$this->testFilesStoragePath}text/smi/normal01.smi")],
            'shifts' => [['from' => '00:00:00', 'to' => '00:00:03', 'milliseconds' => -1000]],
        ])->assertStatus(302)->assertSessionHasErrors(['subtitles' => __('messages.file_can_not_be_partial_shifted')]);
    }

    /** @test */
    function it_redirects_to_results_page_if_multiple_uploads_are_valid()
    {
        $this->expectsJobs(\App\Jobs\FileJobs\ShiftPartialJob::class);

        $response = $this->post(route('shiftPartial'), [
            'subtitles' => [
                $this->createUploadedFile("{$this->testFilesStoragePath}text/ass/normal01.ass"),
                $this->createUploadedFile("{$this->testFilesStoragePath}text/ass/normal01.ass")
            ],
            'shifts' => [['from' => '00:00:00', 'to' => '00:00:03', 'milliseconds' => -1000]],
        ]);

        $fileGroup = FileGroup::findOrFail(1);

        $response->assertStatus(302)
            ->assertRedirect($fileGroup->resultRoute);
    }

    /** @test */
    function it_can_partial_shift_vtt_files()
    {
        [$response, $fileGroup] = $this->postFileJob('shiftPartial', [
            $this->createUploadedFile('text/vtt/three-cues.vtt'),
        ], [
            'shifts' => [
                ['from' => '00:00:00', 'to' => '00:00:06', 'milliseconds' => -1000],
                ['from' => '00:00:10', 'to' => '00:59:59', 'milliseconds' => 1000],
            ],
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(2);
    }

    /** @test */
    function it_can_partial_shift_ass_files()
    {
        [$response, $fileGroup] = $this->postFileJob('shiftPartial', [
            $this->createUploadedFile('text/ass/three-cues.ass'),
        ], [
            'shifts' => [
                ['from' => '00:00:00', 'to' => '00:00:40', 'milliseconds' => -1000],
                ['from' => '00:00:40', 'to' => '00:59:59', 'milliseconds' => 1000],
            ],
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(2);
    }

    /** @test */
    function it_can_partial_shift_ssa_files()
    {
        [$response, $fileGroup] = $this->postFileJob('shiftPartial', [
            $this->createUploadedFile('text/ssa/three-cues.ssa'),
        ], [
            'shifts' => [
                ['from' => '00:00:00', 'to' => '00:00:40', 'milliseconds' => -1000],
                ['from' => '00:00:40', 'to' => '00:59:59', 'milliseconds' => 1000],
            ],
        ]);

        $this->assertSuccessfulFileJobRedirect($response, $fileGroup);

        $this->assertMatchesStoredFileSnapshot(2);
    }
}
