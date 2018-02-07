<?php

namespace App\Jobs\FileJobs;

use App\Support\Facades\TempFile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\FileJobChanged;
use App\Models\FileGroup;
use App\Models\FileJob as FileJobModel;
use App\Models\StoredFile;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class FileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    protected $fileGroup;

    protected $inputStoredFile;

    protected $fileJob;

    /**
     * @param FileGroup $fileGroup
     * @param $file string|UploadedFile
     */
    public function __construct(FileGroup $fileGroup, $file)
    {
        $this->fileGroup = $fileGroup;

        $this->inputStoredFile = StoredFile::getOrCreate($file);

        $originalName = ($file instanceof UploadedFile)
            ? $file->_originalName
            : basename($file);

        $this->fileJob = FileJobModel::create([
            'input_stored_file_id' => $this->inputStoredFile->id,
            'original_name'        => $originalName,
            'file_group_id'        => $this->fileGroup->id,
        ]);
    }

    public function startFileJob()
    {
        $this->fileJob->started_at = now();
    }

    public function finishFileJob(StoredFile $outputStoredFile)
    {
        $this->fileJob->fill([
            'output_stored_file_id' => $outputStoredFile->id,
            'new_extension'         => $this->getNewExtension(),
            'finished_at'           => now(),
        ]);

        return $this->endFileJob();
    }

    public function abortFileJob(string $errorMessage)
    {
        $this->fileJob->fill([
            'error_message' => $errorMessage,
            'finished_at'   => now(),
        ]);

        return $this->endFileJob();
    }

    private function endFileJob()
    {
        $this->fileJob->save();

        $unfinishedJobsCount = $this->fileJob
            ->fileGroup
            ->fileJobs()
            ->whereNull('finished_at')
            ->count();

        if ($unfinishedJobsCount === 0) {
            $this->fileGroup->update(['file_jobs_finished_at' => now()]);
        }

        FileJobChanged::dispatch($this->fileJob);

        TempFile::cleanUp();

        return $this->fileJob;
    }

    public function failed()
    {
        $this->abortFileJob('messages.unknown_error');

        Log::error("FileJob (filejob id: {$this->fileJob->id}) failed! (usually because of a TextEncodingException)");
    }

    abstract public function getNewExtension();
}
