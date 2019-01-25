<?php

namespace Tests\Unit\Controllers;

use App\Models\SubIdx;
use App\Models\SubIdxLanguage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubIdxControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function the_sub_and_idx_file_are_server_side_required()
    {
        $this->postSubIdx([])
            ->assertStatus(302)
            ->assertSessionHasErrors(['sub', 'idx']);
    }

    /** @test */
    function it_rejects_empty_sub_and_idx_files()
    {
        $this->postSubIdx([
                'sub' => $this->createUploadedFile('text/srt/empty.srt', 'empty.sub'),
                'idx' => $this->createUploadedFile('text/srt/empty.srt', 'empty.idx'),
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors([
                'sub' => __('validation.file_is_empty', ['attribute' => 'sub']),
                'idx' => __('validation.file_is_empty', ['attribute' => 'idx']),
            ]);
    }

    /** @test */
    function it_validates_uploaded_sub_and_idx_files()
    {
        $this->postSubIdx([
                'sub' => UploadedFile::fake()->image('movie.sub'),
                'idx' => UploadedFile::fake()->image('text.idx'),
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors([
                'sub' => __('validation.subidx_invalid_sub_mime', ['attribute' => 'sub']),
                'idx' => __('validation.file_is_not_a_textfile',  ['attribute' => 'idx']),
            ]);
    }

    /** @test */
    function it_fails_when_the_subidx_is_not_readable()
    {
        $this->postSubIdx([
                'sub' => $this->createUploadedFile('sub-idx/unreadable.sub'),
                'idx' => $this->createUploadedFile('sub-idx/unreadable.idx'),
            ])
            ->assertStatus(302)
            ->assertSessionHasErrors();

        $this->assertSame(
            ['The sub/idx file can not be read'],
            session('errors')->all()
        );

        $subIdx = SubIdx::findOrFail(1);

        $this->assertNull($subIdx->url_key);
        $this->assertFalse($subIdx->is_readable);
    }

    /** @test */
    function it_redirects_to_the_show_page()
    {
        $response = $this->postSubIdx([
                'sub' => $this->createUploadedFile('sub-idx/error-and-nl.sub'),
                'idx' => $this->createUploadedFile('sub-idx/error-and-nl.idx'),
            ])
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $subIdx = SubIdx::findOrFail(1);

        $response->assertRedirect(route('subIdx.show', $subIdx->url_key));
    }

    /** @test */
    function it_swaps_sub_and_idx_files_if_they_are_put_in_the_wrong_input()
    {
        $response = $this->postSubIdx([
                'sub' => $this->createUploadedFile('sub-idx/error-and-nl.idx'),
                'idx' => $this->createUploadedFile('sub-idx/error-and-nl.sub'),
            ])
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $subIdx = SubIdx::findOrFail(1);

        $response->assertRedirect(route('subIdx.show', $subIdx->url_key));
    }

    /** @test */
    function it_can_show_the_detail_page()
    {
        $subIdx = factory(SubIdx::class)->create();

        $subIdx->languages()->saveMany([
            factory(SubIdxLanguage::class)->states('idle')->make(),
            factory(SubIdxLanguage::class)->states('queued')->make(),
            factory(SubIdxLanguage::class)->states('processing')->make(),
            factory(SubIdxLanguage::class)->states('failed')->make(),
            factory(SubIdxLanguage::class)->states('finished')->make(),
        ]);

        $this->showSubIdx($subIdx)->assertStatus(200);
    }

    /** @test */
    function it_can_download_a_finished_language()
    {
        $this->progressTimeInHours(1);

        $subIdx = factory(SubIdx::class)->create();

        $subIdx->languages()->save(
            $language = factory(SubIdxLanguage::class)->states('finished')->make(['times_downloaded' => 0])
        );

        $originalUpdatedAt = (string) $language->updated_at;

        $this->assertNow($subIdx->refresh()->updated_at);

        $this->progressTimeInHours(1);

        $this->downloadSubIdxLanguage($language)->assertStatus(200);

        $language->refresh();

        $this->assertSame(1, $language->times_downloaded);
        $this->assertNow($language->updated_at);

        // It should not touch the SubIdx relationship when incrementing the "times_downloaded"
        $this->assertSame($originalUpdatedAt, (string) $subIdx->refresh()->updated_at);
    }

    /** @test */
    function getting_the_download_post_url_redirects_to_the_show_page()
    {
        $subIdx = factory(SubIdx::class)->create();

        $subIdx->languages()->save(
            $language = factory(SubIdxLanguage::class)->states('finished')->make()
        );

        $this->get($language->download_url)
            ->assertStatus(302)
            ->assertRedirect(route('subIdx.show', $language->subIdx->url_key));
    }

    private function postSubIdx($data)
    {
        return $this->post(route('subIdx.post'), $data);
    }

    private function showSubIdx($subIdx)
    {
        return $this->get(route('subIdx.show', $subIdx->url_key));
    }

    private function downloadSubIdxLanguage($language)
    {
        return $this->post($language->download_url);
    }
}
