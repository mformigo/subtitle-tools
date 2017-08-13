<?php

namespace App\Jobs;

use App\Models\TextFileJob;
use Carbon\Carbon;
use Faker\Provider\Text;

trait MakesTextFileJobs
{
    /**
     * @var TextFileJob
     */
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

        $this->textFileJob->fill([
            'input_stored_file_id' => $this->storedFile->id,
            'job_options'          => json_encode($this->jobOptions),
            'original_file_name'   => $this->originalName,
            'started_at'           => Carbon::now(),
            'tool_route'           => $this->toolRouteName,
            'url_key'              => str_random(16),
        ]);

        $fromCache = TextFileJob::query()
            ->where('input_stored_file_id', $this->textFileJob->input_stored_file_id)
            ->where('job_options', $this->textFileJob->job_options)
            ->whereNotNull('finished_at');

        if($fromCache->count() > 0) {
            $jobFromCache = $fromCache->firstOrFail();

            $this->textFileJob->fill([
                'error_message'         => $jobFromCache->error_message,
                'finished_at'           => Carbon::now(),
                'new_extension'         => $jobFromCache->new_extension,
                'output_stored_file_id' => $jobFromCache->output_stored_file_id,
            ]);
        }

        return $this->textFileJob;
    }

    public function setTextFileJobError(string $errorMessage)
    {
        $this->textFileJob->fill([
            'error_message' => $errorMessage,
            'finished_at'   => Carbon::now(),
        ]);

        $this->textFileJob->save();

        return $this->textFileJob;
    }
}
