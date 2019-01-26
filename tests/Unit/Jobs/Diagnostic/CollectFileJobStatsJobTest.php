<?php

namespace Tests\Unit\Jobs\Diagnostic;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectFileJobStatsJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_counts_files_in_the_merge_tool_double()
    {

    }

    /** @test */
    function it_has_the_correct_tool_routes_in_config()
    {
        // TODO: figure out a hack to check if "config('st.tool_routes')" is correct.
    }
}
