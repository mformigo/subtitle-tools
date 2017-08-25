<?php

namespace Tests\Feature;

use App\Models\SubIdx;
use Tests\CreatesUploadedFiles;
use Tests\MocksVobSub2Srt;
use Tests\PostsVobSubs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SwapSubAndIdxTest extends TestCase
{
    use DatabaseMigrations, MocksVobSub2Srt, PostsVobSubs, CreatesUploadedFiles;

    /** @test */
    function it_swaps_sub_and_idx()
    {
        $this->withoutJobs();

        $postData = $this->getSubIdxPostData();

        $swappedPostData = [
            'sub' => $postData['idx'],
            'idx' => $postData['sub'],
        ];

        $response = $this->post(route('subIdx'), $swappedPostData);

        $this->assertSame(1, SubIdx::count(), 'Sub/Idx not found, the fields didn\'t get swapped');

        $response->assertStatus(302)
            ->assertRedirect(route('subIdxDetail', ['pageId' => SubIdx::findOrFail(1)->page_id]));
    }
}
