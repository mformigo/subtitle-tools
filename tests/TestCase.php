<?php

namespace Tests;

use App\Models\FileGroup;
use App\Models\StoredFile;
use App\Support\Facades\TempFile;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Snapshots\MatchesSnapshots;
use Illuminate\Contracts\Console\Kernel;

abstract class TestCase extends BaseTestCase
{
    use MatchesSnapshots;

    public $testFilesStoragePath;

    private $testingStorageDirectories = [
        'sub-idx',
        'temporary-files',
        'temporary-dirs',
        'stored-files',
        'diagnostic',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->testFilesStoragePath = base_path('tests/Storage/');

        $this->ensureProperStorageDiskConfig();

        foreach($this->testingStorageDirectories as $dirName) {
            Storage::makeDirectory($dirName);
        }
    }

    public function tearDown()
    {
        $this->ensureProperStorageDiskConfig();

        foreach($this->testingStorageDirectories as $dirName) {
            Storage::deleteDirectory($dirName);
        }

        parent::tearDown();
    }

    private function ensureProperStorageDiskConfig()
    {
        $storagePath = storage_disk_file_path('/');

        if(!ends_with($storagePath, '/testing/')) {
            throw new \Exception("It looks like the storage driver is not set up properly");
        }
    }

    public function dumpSession()
    {
        dd(app('session.store'));
    }

    protected function getSnapshotDirectory(): string
    {
        return $this->testFilesStoragePath.'__snapshots__';
    }

    protected function getFileSnapshotDirectory(): string
    {
        $subDirectory = property_exists($this, 'fileSnapshotDirectory')
            ? DIRECTORY_SEPARATOR.$this->fileSnapshotDirectory
            : '';

        return $this->testFilesStoragePath.'_file-snapshots_'.$subDirectory;
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

        Hash::driver('bcrypt')->setRounds(4);

        return $app;
    }
}
