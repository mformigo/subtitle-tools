<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

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

        if(!ends_with($storagePath, "/testing/")) {
            throw new \Exception("It looks like the storage driver is not set up properly");
        }
    }

    public function dumpSession()
    {
        dd(app('session.store'));
    }

}
