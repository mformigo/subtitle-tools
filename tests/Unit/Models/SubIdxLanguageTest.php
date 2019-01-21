<?php

namespace Tests\Unit\Models;

use App\Models\SubIdx;
use App\Models\SubIdxLanguage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubIdxLanguageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_touches_the_subidx_model_when_updated()
    {
        $subIdx = factory(SubIdx::class)->create();

        $this->progressTimeInHours(1);

        $subIdx->languages()->save(
            $language = factory(SubIdxLanguage::class)->state('idle')->make()
        );

        $this->assertNow($subIdx->refresh()->updated_at);

        $this->progressTimeInHours(1);

        $language->update(['queued_at' => now()]);

        $this->assertNow($subIdx->refresh()->updated_at);
    }
}
