<?php

namespace Tests\Feature;

use Tests\TestCase;

class RedirectsTest extends TestCase
{
    /** @test */
    function it_performs_301_redirects()
    {
        $this->get('/format-converter')->assertStatus(301)->assertRedirect(route('convert-to-srt'));
        $this->get('/convert-to-srt')->assertStatus(301)->assertRedirect(route('convert-to-srt'));
        $this->get('/fo...')->assertStatus(301)->assertRedirect(route('convert-to-srt'));
        $this->get('/tools')->assertStatus(301)->assertRedirect(route('home'));
    }
}
