<?php

namespace Tests\Browser;

use Tests\Browser\Pages\SubIdxPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubIdxTest extends DuskTestCase
{
    /** @test */
    function sub_and_idx_files_are_client_side_required()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new SubIdxPage)
                ->assertMissing('@errors')
                ->click('@submit')
                // the 'required' attribute on the inputs should prevent the form from submitting
                ->on(new SubIdxPage)
                ->assertMissing('@errors');
        });
    }

}
