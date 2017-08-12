<?php

namespace App\Jobs;

use App\Models\TextFileJob;
use Carbon\Carbon;

trait MakesTextFileJobs
{
    protected $textFileJob;

    protected $storedFile;

    protected $originalName;

    protected $toolRouteName;

    protected $jobOptions = [
        'actions' => [],
    ];

    public function makeTextFileJob()
    {
        $this->textFileJob = new TextFileJob();

        $this->textFileJob->started_at = Carbon::now();

        $this->textFileJob->input_stored_file_id = $this->storedFile->id;

        $this->textFileJob->original_file_name = $this->originalName;

        $this->textFileJob->url_key = str_random(16);

        $this->textFileJob->tool_route = $this->toolRouteName;

        $this->textFileJob->job_options = json_encode($this->jobOptions);

        $fromCache = TextFileJob::query()
            ->where('input_stored_file_id', $this->textFileJob->input_stored_file_id)
            ->where('job_options', $this->textFileJob->job_options)
            ->whereNotNull('finished_at');

        if($fromCache->count() > 0) {
            $jobFromCache = $fromCache->firstOrFail();

            $this->textFileJob->new_extension = $jobFromCache->new_extension;

            $this->textFileJob->output_stored_file_id = $jobFromCache->output_stored_file_id;

            $this->textFileJob->error_message = $jobFromCache->error_message;

            $this->textFileJob->finished_at = Carbon::now();
        }

        return $this->textFileJob;
    }

    public function setTextFileJobError(string $errorMessage)
    {
        $this->textFileJob->error_message = $errorMessage;

        $this->textFileJob->finished_at = Carbon::now();

        $this->textFileJob->save();

        return $this->textFileJob;
    }
}
