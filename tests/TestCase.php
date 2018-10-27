<?php

namespace Tests;

use App\Models\FileGroup;
use App\Models\StoredFile;
use App\Support\Facades\TempFile;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\DB;
use SjorsO\MocksTime\MocksTime;
use Spatie\Snapshots\MatchesSnapshots;
use Illuminate\Contracts\Console\Kernel;

abstract class TestCase extends BaseTestCase
{
    use MatchesSnapshots, MocksTime;

    public $testFilesStoragePath;

    public function setUp()
    {
        parent::setUp();

        $this->testFilesStoragePath = base_path('tests/Files/');
    }

    protected function getSnapshotDirectory(): string
    {
        return $this->getFileSnapshotDirectory();
    }

    protected function getFileSnapshotDirectory(): string
    {
        $subDirectory = property_exists($this, 'snapshotDirectory')
            ? DIRECTORY_SEPARATOR.$this->snapshotDirectory
            : '';

        return $this->testFilesStoragePath.'_snapshots_'.$subDirectory;
    }

    public function assertMatchesFileSnapshot($file)
    {
        if ($file instanceof StoredFile) {
            $temporaryFilePath = TempFile::makeFilePath().'.txt';

            // Git changes line endings to \n, but we save files with \r\n, so we have to change them
            $lines = preg_split("/\r\n|\n|\r/",
                file_get_contents($file->file_path)
            );

            file_put_contents($temporaryFilePath, implode("\n", $lines));

            $file = $temporaryFilePath;
        }

        $this->doFileSnapshotAssertion($file);
    }

    public function assertMatchesStoredFileSnapshot($storedFileId)
    {
        $storedFile = StoredFile::findOrFail($storedFileId);

        $this->assertMatchesFileSnapshot($storedFile);
    }

    /**
     * Assert that the file job controller redirected to the file group result page.
     *
     * @param TestResponse $response
     * @param FileGroup $fileGroup
     */
    protected function assertSuccessfulFileJobRedirect(TestResponse $response, FileGroup $fileGroup)
    {
        $response->assertStatus(302)->assertRedirect($fileGroup->result_route);
    }

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Sqlite has foreign key constraints disabled by default
        DB::connection()->getSchemaBuilder()->enableForeignKeyConstraints();

        return $app;
    }
}
