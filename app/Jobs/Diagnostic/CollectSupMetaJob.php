<?php

namespace App\Jobs\Diagnostic;

use App\Jobs\BaseJob;
use App\Models\Diagnostic\SupJobMeta;
use App\Models\SupJob;
use SjorsO\Sup\SupFile;

class CollectSupMetaJob extends BaseJob
{
    protected $supJob;

    public function __construct(SupJob $supJob)
    {
        $this->supJob = $supJob;
    }

    public function handle()
    {
        $sup = SupFile::open($this->supJob->inputStoredFile->file_path);

        SupJobMeta::create([
            'sup_job_id' => $this->supJob->id,
            'format'     => class_basename($sup),
            'cue_count'  => count($sup->getCues()),
        ]);
    }
}
