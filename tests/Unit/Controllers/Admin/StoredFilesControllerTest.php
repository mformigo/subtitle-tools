<?php

namespace Tests\Unit\Controllers\Admin;

use App\Models\StoredFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoredFilesControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_can_show_a_stored_file()
    {

    }

    /** @test */
    function it_can_download_stored_files()
    {

    }

    /** @test */
    function it_can_delete_stored_files()
    {
        $s1 = factory(StoredFile::class)->create();
        $s2 = factory(StoredFile::class)->create();

        $this->adminLogin()
            ->delete(route('admin.storedFiles.delete'), ['id' => $s2->id])
            ->assertSessionHasNoErrors()
            ->assertStatus(302);

        $this->assertNotNull(StoredFile::find($s1->id));
        $this->assertNull(StoredFile::find($s2->id));
    }
}
