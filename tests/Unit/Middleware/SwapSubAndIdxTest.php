<?php

namespace Tests\Unit\Middleware;

use App\Models\SubIdx;
use Tests\CreatesUploadedFiles;
use Tests\MocksVobSub2Srt;
use Tests\PostsVobSubs;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SwapSubAndIdxTest extends TestCase
{
    use RefreshDatabase, MocksVobSub2Srt, PostsVobSubs, CreatesUploadedFiles;

    /** @test */
    function it_swaps_sub_and_idx()
    {
        $this->withoutJobs();

        $postData = $this->getSubIdxPostData();

        $response = $this->post(route('subIdx'), [
            'sub' => $postData['idx'],
            'idx' => $postData['sub'],
        ]);

        $this->assertSame(1, SubIdx::count(), 'Sub/Idx not found, the fields didn\'t get swapped');

        $response->assertStatus(302)
            ->assertRedirect(route('subIdx.show', SubIdx::findOrFail(1)->url_key));
    }
}
