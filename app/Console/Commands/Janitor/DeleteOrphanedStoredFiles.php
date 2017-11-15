<?php

namespace App\Console\Commands\Janitor;

use App\Models\StoredFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;

class DeleteOrphanedStoredFiles extends Command
{
    protected $signature = 'st:delete-orphaned-files';

    protected $description = 'Deletes stored files with no database reference';

    public function handle()
    {
        $this->info('Removing orphaned stored files...');

        $this->deleteOrphanedStoredFiles();

        $this->deleteEmptyStoredFileDirectories();

        $this->info('Done!');
    }

    protected function deleteOrphanedStoredFiles()
    {
        $databaseStoredFiles = StoredFile::pluck('storage_file_path')->all();

        $existingStoredFiles = array_filter(Storage::allFiles('stored-files/'), function ($fileName) {
            // Remove .gitignore
            return ! starts_with($fileName, 'stored-files/.');
        });

        $orphanedStoredFiles = array_diff($existingStoredFiles, $databaseStoredFiles);

        $this->comment('Database has '.count($databaseStoredFiles).' stored files, disk has '.count($existingStoredFiles).' stored files');

        $this->comment('Deleting '.count($orphanedStoredFiles).' orphaned stored files...');

        foreach($orphanedStoredFiles as $orphanFileName) {
            Storage::delete($orphanFileName);
        }
    }

    protected function deleteEmptyStoredFileDirectories()
    {
        $allDirectories = Storage::allDirectories('stored-files/');

        collect($allDirectories)
            ->sortByDesc(function ($directoryPath) {
                // sort by string length so we delete empty subdirectories before the main directory
                return strlen($directoryPath);
            })
            ->each(function ($directoryPath) {
                // don't filter the array, directories might become empty when we delete subdirectories
                if($this->isEmptyStorageDirectory($directoryPath)) {
                   Storage::deleteDirectory($directoryPath);
               }
            });
    }

    protected function isEmptyStorageDirectory($storagePath)
    {
        $path = storage_disk_file_path($storagePath);

        if(! file_exists($path) || ! is_dir($path)) {
            throw new Exception('Storage path is not a directory: '.$storagePath);
        }

        $directoryEntries = array_diff(scandir($path), ['.', '..']);

        return count($directoryEntries) === 0;
    }
}
