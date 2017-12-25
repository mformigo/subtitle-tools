<?php

namespace Tests;

use App\Models\StoredFile;
use App\Support\Facades\TempFile;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
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

    public function assertMatchesFileSnapshot($file)
    {
        if($file instanceof StoredFile) {
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

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Reduce bcrypt strength to speed up the tests
        Hash::setRounds(4);

        return $app;
    }
}
