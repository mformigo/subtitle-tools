<?php

namespace App\Console\Commands\Janitor;

use App\Models\FileGroup;
use App\Models\FileJob;
use App\Models\StoredFile;
use App\Models\StoredFileMeta;
use App\Models\SubIdxLanguage;
use App\Models\SupJob;
use Illuminate\Console\Command;

class CleanDisk extends Command
{
    protected $signature = 'st:clean-disk';

    protected $description = 'Deletes all file groups, file jobs their stored files';

    protected $dontDeleteIds = [];

    public function handle()
    {
        if(! app()->isDownForMaintenance() && ! app()->environment('local')) {
            $this->error('Can only clean disk in maintenance mode');
            return;
        }

        StoredFileMeta::truncate();

        // We want to keep stored files that are part of sub/idx and sup jobs
        $this->dontDeleteIds = $this->findStoredFileIdsToKeep();

        $this->deleteFileJobs();

        $this->deleteFileGroupArchives();

        FileGroup::truncate();

        $this->call('st:calculate-disk-usage');

        $this->info('Don\'t forget to run php artisan up!');
        $this->info('Don\'t forget to run php artisan up!!');
        $this->info('Don\'t forget to run php artisan up!!!');
    }

    protected function deleteFileJobs()
    {
        $fileJobs = FileJob::all();

        $this->info('Deleting file jobs...');
        $progressBar = $this->output->createProgressBar(count($fileJobs));

        foreach($fileJobs as $fileJob) {
            $this->maybeDeleteStoredFile($fileJob->inputStoredFile);

            $this->maybeDeleteStoredFile($fileJob->outputStoredFile);

            $progressBar->advance();
        }

        FileJob::truncate();

        $progressBar->finish();
        $this->info(' done!' . "\n");
    }

    protected function deleteFileGroupArchives()
    {
        $fileGroups = FileGroup::query()
            ->whereNotNull('archive_stored_file_id')
            ->get();

        $this->info('Deleting file group archives...');
        $progressBar = $this->output->createProgressBar(count($fileGroups));

        foreach($fileGroups as $fileGroup) {
            $this->maybeDeleteStoredFile($fileGroup->archiveStoredFile);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info(' done!' . "\n");
    }

    protected function maybeDeleteStoredFile($storedFile)
    {
        if($storedFile === null || ! $storedFile instanceof StoredFile) {
            return;
        }

        $id = (int)$storedFile->id;

        if (in_array($id, $this->dontDeleteIds, true)) {
            return;
        }

        $filePath = $storedFile->file_path;

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $storedFile->delete();
    }

    protected function findStoredFileIdsToKeep()
    {
        // don't delete these stored files:
        //   sub_idx_languages -> output_stored_file_id
        //   sup_jobs -> output_stored_file_id
        //   sup_jobs -> input_stored_file_id

        $subIds = SubIdxLanguage::query()
            ->whereNotNull('output_stored_file_id')
            ->get()
            ->map(function (SubIdxLanguage $subIdxLanguage) {
                return (int)$subIdxLanguage->output_stored_file_id;
            })
            ->all();

        $supIds = SupJob::all()
            ->map(function (SupJob $supJob) {
                if ($supJob->output_stored_file_id === null) {
                    return [(int)$supJob->input_stored_file_id];
                }

                return [(int)$supJob->input_stored_file_id, (int)$supJob->output_stored_file_id];
            })
            ->flatten()
            ->all();

        $dontDeleteIds = array_merge($subIds, $supIds);

        $dontDeleteIds = array_unique($dontDeleteIds);

        return $dontDeleteIds;
    }
}
