<?php

namespace Tests\Unit;

use Tests\TestCase;

class VueTest extends TestCase
{
    /** @test */
    function it_has_the_correct_hardcoded_routes()
    {
        $this->assertSame(
            'http://st.test/api/v1/sub-idx/URL_KEY/languages',
            route('api.subIdx.languages', 'URL_KEY')
        );

    }
}
