<?php

namespace Tests\Feature;

use Tests\MocksVobSub2Srt;
use Tests\PostsVobSubs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubIdxControllerTest extends TestCase
{
    use DatabaseMigrations;
    use MocksVobSub2Srt;
    use PostsVobSubs;

    /** @test */
    function it_returns_queued_language_extract_information()
    {
        $this->withoutJobs();

        $subIdx = $this->postVobSub();

        $response = $this->get(route('api-sub-idx-languages', ['pageId' => $subIdx->page_id]));

        $response->assertStatus(200)
            ->assertJson([
                ['index' => 0, 'downloadUrl' => false],
                ['index' => 1, 'downloadUrl' => false],
            ]);
    }

    /** @test */
    function it_returns_finished_and_failed_language_extract_information()
    {
        $this->useMockVobSub2Srt();

        $subIdx = $this->postVobSub();

        $response = $this->get(route('api-sub-idx-languages', ['pageId' => $subIdx->page_id]));

        $response->assertStatus(200)
            ->assertJson([
                ['index' => 0, 'downloadUrl' => route('sub-idx-dl', ['pageId' => $subIdx->page_id, 'index' => 0])],
                ['index' => 1, 'downloadUrl' => route('sub-idx-dl', ['pageId' => $subIdx->page_id, 'index' => 1])],
                ['index' => 2, 'downloadUrl' => false],
                ['index' => 3, 'downloadUrl' => false],
            ]);
    }

}
