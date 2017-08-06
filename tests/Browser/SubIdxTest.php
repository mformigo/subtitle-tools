<?php

namespace Tests\Browser;

use Tests\Browser\Pages\SubIdxPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SubIdxTest extends DuskTestCase
{
    /** @test */
    public function sub_and_idx_files_are_required()
    {
        $this->browse(function (Browser $browser) {
            $subError = __('validation.required', ['attribute' => 'sub']);
            $idxError = __('validation.required', ['attribute' => 'idx']);

            $browser->visit(new SubIdxPage)
                ->assertMissing('#Errors')
                ->assertDontSee($subError)
                ->assertDontSee($idxError)
                ->click('@submit')
                ->on(new SubIdxPage)
                ->assertVisible('#Errors')
                ->assertSee($subError)
                ->assertSee($idxError);
        });
    }
}
