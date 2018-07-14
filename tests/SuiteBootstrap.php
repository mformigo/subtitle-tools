<?php

namespace Tests;

use PHPUnit\Runner\AfterLastTestHook;
use PHPUnit\Runner\BeforeFirstTestHook;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SuiteBootstrap implements BeforeFirstTestHook, AfterLastTestHook
{
    private $storageDirectory = './storage/testing/';

    private $testingStorageDirectories = [
        'sub-idx',
        'temporary-files',
        'temporary-dirs',
        'stored-files',
        'diagnostic',
    ];

    public function executeBeforeFirstTest(): void
    {
        foreach($this->testingStorageDirectories as $dirName) {
            $directory = $this->storageDirectory.$dirName;

            if (! file_exists($directory)) {
                mkdir($directory);
            }
        }
    }

    public function executeAfterLastTest(): void
    {
        foreach($this->testingStorageDirectories as $dirName) {
            $this->deleteDirectory($this->storageDirectory.$dirName);
        }
    }

    protected function deleteDirectory($directoryPath)
    {
        $directoryIterator = new RecursiveDirectoryIterator($directoryPath, RecursiveDirectoryIterator::SKIP_DOTS);

        $fileIterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::CHILD_FIRST);

        foreach($fileIterator as $file) {
            $file->isDir()
                ? rmdir($file->getRealPath())
                : unlink($file->getRealPath());
        }

        rmdir($directoryPath);
    }
}
