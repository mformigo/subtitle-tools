<?php

namespace App\Jobs\Janitor;

use App\Jobs\BaseJob;
use App\Jobs\Diagnostic\CalculateDiskUsageJob;
use App\Models\StoredFile;
use RuntimeException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PruneStoredFilesJob extends BaseJob implements ShouldQueue
{
    public function handle()
    {
        if ($this->isOnCheckedMigration()) {
            $this->deleteUnreferencedStoredFileRecords();
        }

        $this->deleteOrphanedStoredFiles();

        $this->deleteEmptyStoredFileDirectories();

        CalculateDiskUsageJob::dispatch();
    }

    /**
     * If a new table is added that uses stored files, and we forget to update this command,
     * it will take me ages to figure out stuff is broken. Therefor we only delete records
     * from the database if the database is on a migration we specified.
     *
     * @return bool
     */
    private function isOnCheckedMigration()
    {
        $lastMigration = DB::table('migrations')->orderByDesc('id')->first()->migration;

        if ($lastMigration === config('st.checked-migration')) {
            return true;
        }

        info('PruneStoredFiles did not delete any database records because it is not on a checked migration');

        return false;
    }

    /**
     * Delete stored files from the database that are not referenced by any other record.
     */
    private function deleteUnreferencedStoredFileRecords()
    {
        $storedFileColumns = [
            'file_groups' => ['archive_stored_file_id'],
            'file_jobs' => ['input_stored_file_id', 'output_stored_file_id'],
            'sub_idx_languages' => ['output_stored_file_id'],
            'sup_jobs' => ['input_stored_file_id', 'output_stored_file_id'],
        ];

        $referencedStoredFileIds = [];

        foreach ($storedFileColumns as $table => $columns) {
            $ids = DB::table($table)
                ->select($columns)
                ->get()
                ->map(function ($record) {
                    return array_values((array) $record);
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

        foreach (array_chunk($unreferencedIds, 10000) as $chunkOfUnreferencedIds) {
            StoredFile::whereIn('id', $chunkOfUnreferencedIds)->delete();
        }
    }

    /**
     * Delete all stored file on the disk that do not have a reference in the database.
     */
    private function deleteOrphanedStoredFiles()
    {
        $existingStoredFiles = array_filter(Storage::allFiles('stored-files/'), function ($fileName) {
            return ! starts_with($fileName, 'stored-files/.');
        });

        // Check the database after the disk to not accidentally delete a newly uploaded file
        $databaseStoredFiles = StoredFile::pluck('storage_file_path')->all();

        $orphanedStoredFiles = array_diff($existingStoredFiles, $databaseStoredFiles);

        foreach ($orphanedStoredFiles as $orphanFileName) {
            Storage::delete($orphanFileName);
        }
    }

    private function deleteEmptyStoredFileDirectories()
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

    private function isEmptyStorageDirectory($storagePath)
    {
        $path = storage_disk_file_path($storagePath);

        if (! file_exists($path) || ! is_dir($path)) {
            throw new RuntimeException('Storage path is not a directory: '.$storagePath);
        }

        $directoryEntries = array_diff(scandir($path), ['.', '..']);

        return count($directoryEntries) === 0;
    }
}
