<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SubIdx extends TestCase
{
    /** @test */
    function sub_and_idx_file_are_server_side_required()
    {
        $response = $this->post(route('sub-idx-index'));

        $response->assertStatus(302)
                 ->assertSessionHasErrors([
                     'sub' => __('validation.required', ['attribute' => 'sub']),
                     'idx' => __('validation.required', ['attribute' => 'idx']),
                 ]);
    }

}
