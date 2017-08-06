<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

class SubIdxPage extends BasePage
{
    public function url()
    {
        return '/convert-sub-idx-to-srt-online';
    }

    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url())
                ->assertSeeIn('h1', 'Convert Sub/Idx to Srt');
    }

    public function elements()
    {
        return [
            '@submit'    => 'button[type=submit]',
            '@sub-field' => 'input[name=sub]',
            '@idx-field' => 'input[name=idx]',
        ];
    }

}
