<?php

namespace Tests\Feature;

use Tests\TestCase;

class RedirectsTest extends TestCase
{
    /** @test */
    function it_performs_301_redirects()
    {
        $this->get('/format-converter')        ->assertStatus(301)->assertRedirect(route('convertToSrt'));
        $this->get('/convert-to-srt')          ->assertStatus(301)->assertRedirect(route('convertToSrt'));
        $this->get('/fo...')                   ->assertStatus(301)->assertRedirect(route('convertToSrt'));
        $this->get('/tools')                   ->assertStatus(301)->assertRedirect(route('home'));
        $this->get('/chinese-to-pinyin')       ->assertStatus(301)->assertRedirect(route('pinyin'));
        $this->get('/subtitle-shift')          ->assertStatus(301)->assertRedirect(route('shift'));
        $this->get('/partial-subtitle-shifter')->assertStatus(301)->assertRedirect(route('shiftPartial'));
        $this->get('/multi-subtitle-shift')    ->assertStatus(301)->assertRedirect(route('shiftPartial'));
        $this->get('/convert-to-utf8')         ->assertStatus(301)->assertRedirect(route('convertToUtf8'));
        $this->get('/c...')                    ->assertStatus(301)->assertRedirect(route('convertToSrt'));
        $this->get('/convert-to-srt-on...')    ->assertStatus(301)->assertRedirect(route('convertToSrt'));
    }
}
