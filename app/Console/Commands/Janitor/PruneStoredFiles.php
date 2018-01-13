<?php

namespace App\Console\Commands\Janitor;

use App\Models\StoredFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;

class PruneStoredFiles extends Command
{
    protected $signature = 'st:prune-stored-files';

    protected $description = 'Deletes unreferenced stored files from database and disk';

    public function handle()
    {
        $this->info('Pruning stored files...');

        if ($this->isOnCheckedMigration()) {
            $this->deleteUnreferencedStoredFileRecords();
        }

        $this->deleteOrphanedStoredFiles();

        $this->deleteEmptyStoredFileDirectories();

        $this->call('st:calculate-disk-usage');
    }

    /**
     * Delete stored files from the database that are not referenced by any other record
     */
    protected function deleteUnreferencedStoredFileRecords()
    {
        $storedFileColumns = [
            'file_groups'       => ['archive_stored_file_id'],
            'file_jobs'         => ['input_stored_file_id', 'output_stored_file_id'],
            'sub_idx_languages' => ['output_stored_file_id'],
            'sup_jobs'          => ['input_stored_file_id', 'output_stored_file_id'],
        ];

        $referencedStoredFileIds = [];

        foreach ($storedFileColumns as $table => $columns) {
            $ids = DB::table($table)
                ->select($columns)
                ->get()
                ->map(function ($record) {
                    return array_values((array)$record);
                })
                ->flatten()
                ->filter()
                ->unique()
                ->all();

            $referencedStoredFileIds = array_merge($referencedStoredFileIds, $ids);
        }

        $referencedStoredFileIds = array_unique($referencedStoredFileIds);

        $allStoredFileIds = StoredFile::pluck('id')->all();

        $unreferencedIds = array_diff($allStoredFileIds, $referencedStoredFileIds);

        $this->comment('Database has '.count($allStoredFileIds).' stored files, '.count($referencedStoredFileIds).' are referenced');

        $this->comment('Deleting '.count($unreferencedIds).' unreferenced stored file records...');

        StoredFile::whereIn('id', $unreferencedIds)->delete();
    }

    /**
     * If a new table is added that uses stored files, and we forget to update this command,
     * it will take me ages to figure out stuff is broken. Therefor we only delete records
     * from the database if the database is on a migration we specified
     *
     * @return bool
     */
    protected function isOnCheckedMigration()
    {
        $lastMigration = DB::table('migrations')->orderBy('id', 'desc')->first()->migration;

        if ($lastMigration === config('st.checked-migration')) {
            return true;
        }

        $this->error('Not on a checked migration, not deleting records');

        info('PruneStoredFiles did not delete any database records because it is not on a checked migration');

        return false;
    }

    protected function deleteOrphanedStoredFiles()
    {
        $existingStoredFiles = array_filter(Storage::allFiles('stored-files/'), function ($fileName) {
            // Remove .gitignore
            return ! starts_with($fileName, 'stored-files/.');
        });

        // Check the database after the disk to not accidentally delete a newly uploaded file
        $databaseStoredFiles = StoredFile::pluck('storage_file_path')->all();

        $orphanedStoredFiles = array_diff($existingStoredFiles, $databaseStoredFiles);

        $this->comment('Disk has '.count($existingStoredFiles).' stored files');

        $this->comment('Deleting '.count($orphanedStoredFiles).' orphaned stored files...');

        foreach ($orphanedStoredFiles as $orphanFileName) {
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
                if ($this->isEmptyStorageDirectory($directoryPath)) {
                   Storage::deleteDirectory($directoryPath);
               }
            });
    }

    protected function isEmptyStorageDirectory($storagePath)
    {
        $path = storage_disk_file_path($storagePath);

        if (! file_exists($path) || ! is_dir($path)) {
            throw new Exception('Storage path is not a directory: '.$storagePath);
        }

        $directoryEntries = array_diff(scandir($path), ['.', '..']);

        return count($directoryEntries) === 0;
    }
}
