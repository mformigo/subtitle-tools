<?php

namespace Tests\Unit\Jobs\Janitor;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PruneStoredFilesJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function the_checked_migration_is_correct()
    {
        $lastMigration = DB::table('migrations')->orderByDesc('id')->first()->migration;

        $this->assertSame(
            config('st.checked-migration'),
            $lastMigration,
            'The migration written in "config/st.php" does not match the last migration'
        );
    }

    /** @test */
    function it_()
    {
        // todo: test
    }

    /** @test */
    function it_deletes_directories_that_become_empty_after_deleting_files()
    {

    }
}
