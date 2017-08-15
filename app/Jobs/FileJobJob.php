<?php

namespace App\Jobs;

use App\Events\FileJobChanged;
use App\Models\FileGroup;
use App\Models\FileJob;
use App\Models\StoredFile;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

abstract class FileJobJob
{
    protected $fileGroup;

    protected $inputStoredFile;

    protected $originalName;

    protected $fileJob;

    public abstract function getNewExtension();

    /**
     * @param FileGroup $fileGroup
     * @param $file string|UploadedFile
     */
    public function __construct(FileGroup $fileGroup, $file)
    {
        $this->fileGroup = $fileGroup;

        $this->inputStoredFile = StoredFile::getOrCreate($file);

        $this->originalName = ($file instanceof UploadedFile) ? $file->getClientOriginalName() : basename($file);

        $this->fileJob = FileJob::create([
            'input_stored_file_id' => $this->inputStoredFile->id,
            'original_name'   => $this->originalName,
            'file_group_id' => $this->fileGroup->id,
        ]);
    }

    public function startFileJob()
    {
        $this->fileJob->started_at = Carbon::now();
    }

    public function finishFileJob(StoredFile $outputStoredFile)
    {
        $this->fileJob->fill([
            'output_stored_file_id' => $outputStoredFile->id,
            'new_extension' => $this->getNewExtension(),
            'finished_at' => Carbon::now(),
        ]);

        return $this->endFileJob();
    }

    public function abortFileJob(string $errorMessage)
    {
        $this->fileJob->fill([
            'error_message' => $errorMessage,
            'finished_at'   => Carbon::now(),
        ]);

        return $this->endFileJob();
    }

    private function endFileJob()
    {
        $this->fileJob->save();

        event(new FileJobChanged($this->fileJob));

        return $this->fileJob;
    }
}
