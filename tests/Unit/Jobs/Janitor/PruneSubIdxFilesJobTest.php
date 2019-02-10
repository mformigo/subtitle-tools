<?php

namespace Tests\Unit\Jobs\Janitor;

use App\Jobs\Janitor\PruneSubIdxFilesJob;
use App\Models\SubIdx;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PruneSubIdxFilesJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_deletes_orphaned_sub_idx_directories()
    {
        Storage::put($file1 = 'sub-idx/2017-34/1503845555-aaaaaaaaaaaa/file.sub-idx', '');
        Storage::put($file2 = 'sub-idx/2017-34/1504093368-bbbbbbbbbbbb/file.sub-idx', '');
        Storage::put($file3 = 'sub-idx/2017-35/1504152322-cccccccccccc/file.sub-idx', '');
        Storage::put($file4 = 'sub-idx/2017-35/1504152322-dddddddddddd/file.sub-idx', '');
        Storage::put($file5 = 'sub-idx/2017-36/1504185644-eeeeeeeeeeee/file.sub-idx', '');

        $subIdx1 = factory(SubIdx::class)->create(['store_directory' => 'sub-idx/2017-34/1503845555-aaaaaaaaaaaa/']);
        $subIdx2 = factory(SubIdx::class)->create(['store_directory' => 'sub-idx/2017-34/1504093368-bbbbbbbbbbbb/']);
        $subIdx3 = factory(SubIdx::class)->create(['store_directory' => 'sub-idx/2017-35/1504152322-cccccccccccc/']);
        $subIdx4 = factory(SubIdx::class)->create(['store_directory' => 'sub-idx/2017-35/1504152322-dddddddddddd/']);
        $subIdx5 = factory(SubIdx::class)->create(['store_directory' => 'sub-idx/2017-36/1504185644-eeeeeeeeeeee/']);

        (new PruneSubIdxFilesJob)->handle();
        Storage::assertExists([$file1, $file2, $file3, $file4, $file5]);

        $subIdx1->delete();
        $subIdx3->delete();

        (new PruneSubIdxFilesJob)->handle();
        Storage::assertExists([$file2, $file4, $file5]);
        Storage::assertMissing([$file1, $file3]);

        $subIdx2->delete();

        (new PruneSubIdxFilesJob)->handle();
        Storage::assertExists([$file4, $file5]);
        Storage::assertMissing([$file1, $file2, $file3]);

        // The empty base directory was also deleted
        Storage::assertMissing('sub-idx/2017-34');
    }

    /** @test */
    function sub_idxes_are_stored_with_the_correct_storage_directory_format()
    {
        $sub = $this->createUploadedFile('sub-idx/error-and-nl.sub');
        $idx = $this->createUploadedFile('sub-idx/error-and-nl.idx');

        $subIdx = SubIdx::getOrCreateFromUpload($sub, $idx);

        $storageDirectory = $subIdx->store_directory;

        $this->assertTrue(
            (bool) preg_match('~^sub-idx/\d\d\d\d-\d\d?\d?/~', $storageDirectory)
        );

        $this->assertStringEndsWith('/', $storageDirectory);
    }
}
