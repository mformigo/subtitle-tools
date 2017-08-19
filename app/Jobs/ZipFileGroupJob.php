<?php

namespace App\Jobs;

use App\Models\FileGroup;
use App\Models\StoredFile;
use App\Utils\TempFile;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use ZipArchive;

class ZipFileGroupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 120;

    protected $fileGroup;

    public function __construct(FileGroup $fileGroup)
    {
        $this->fileGroup = $fileGroup;
    }

    public function handle()
    {
        $newZip = new ZipArchive();

        $zipTempFilePath = (new TempFile())->makeFilePath('zip');

        $createSuccess = $newZip->open($zipTempFilePath, ZipArchive::CREATE);

        if($createSuccess !== true) {
            return $this->failed('messages.zip.create_failed');
        }

        $fileJobs = $this->fileGroup->fileJobs;

        foreach($fileJobs as $fileJob) {
            if($fileJob->output_stored_file_id !== null) {
                // TODO: make sure it doesnt add the same name twice
                $newZip->addFile($fileJob->outputStoredFile->filePath, $fileJob->original_name);
            }
        }

        if($newZip->numFiles === 0) {
            return $this->failed('messages.zip.no_files_added');
        }

        $closeSuccess = $newZip->close();

        if($closeSuccess !== true) {
            return $this->failed('messages.zip.close_failed');
        }

        $storedFile = StoredFile::getOrCreate($zipTempFilePath);

        $this->fileGroup->update([
            'archive_stored_file_id' => $storedFile->id,
            'archive_finished_at' => Carbon::now(),
        ]);

        unlink($zipTempFilePath);

        return $this->fileGroup;
    }

    public function failed($errorMessage = 'messages.zip.unknown_error')
    {
        $this->fileGroup->update([
            'archive_error' => $errorMessage,
            'archive_finished_at' => Carbon::now(),
        ]);

        return $this->fileGroup;
    }
}
