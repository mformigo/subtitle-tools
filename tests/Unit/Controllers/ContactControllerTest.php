<?php

namespace Tests\Unit\Controllers;

use Illuminate\Support\Carbon;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    protected $feedbackLogFilePath;

    public function setUp()
    {
        parent::setUp();

        $this->feedbackLogFilePath = storage_path('logs/feedback.log');

        Carbon::setTestNow('2018-07-14 12:30:00');

        if (file_exists($this->feedbackLogFilePath)) {
            unlink($this->feedbackLogFilePath);
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        if (file_exists($this->feedbackLogFilePath)) {
            unlink($this->feedbackLogFilePath);
        }
    }

    /** @test */
    function it_sends_feedback()
    {
        $this->post(route('contact.post'), [
            'message' => 'Message Text',
            'email'   => 'Email Text',
            'captcha' => '6',
        ])
        ->assertStatus(200)
        ->assertSessionHasNoErrors()
        ->assertSee('Thank you for your message');

        $feedbackLines = read_lines($this->feedbackLogFilePath);

        $this->assertSame([
            '<strong>Saturday, the 14th of July at 12:30</strong><br>127.0.0.1<br><p>email: Email Text<br><br>Message Text</p><br><br>',
        ], $feedbackLines);
    }

    /** @test */
    function message_is_required()
    {
        $this->post(route('contact.post'), [
            'email'   => 'Email Text',
            'captcha' => '6',
        ])
        ->assertStatus(302)
        ->assertSessionHasErrors('message');
    }

    /** @test */
    function captcha_must_be_correct()
    {
        $this->post(route('contact.post'), [
            'message' => 'content',
            'captcha' => '4',
        ])
        ->assertStatus(302)
        ->assertSessionHasErrors('captcha');
    }

    /** @test */
    function email_field_is_optional()
    {
        $this->post(route('contact.post'), [
            'message' => 'Message Text',
            'captcha' => '6',
        ])
        ->assertStatus(200)
        ->assertSessionHasNoErrors()
        ->assertSee('Thank you for your message');

        $feedbackLines = read_lines($this->feedbackLogFilePath);

        $this->assertSame([
            '<strong>Saturday, the 14th of July at 12:30</strong><br>127.0.0.1<br><p>email: (none)<br><br>Message Text</p><br><br>'
        ], $feedbackLines);
    }

    /** @test */
    function it_has_the_correct_input_names()
    {
        $this->get(route('contact'))
            ->assertSee('name="message"')
            ->assertSee('name="email"')
            ->assertSee('name="captcha"');
    }
}
