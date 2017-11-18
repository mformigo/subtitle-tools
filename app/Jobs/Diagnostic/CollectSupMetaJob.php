<?php

namespace App\Jobs\Diagnostic;

use App\Jobs\BaseJob;
use App\Models\Diagnostic\SupJobMeta;
use App\Models\SupJob;
use Exception;
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
        try {
            $sup = SupFile::open($this->supJob->inputStoredFile->file_path);

            if($sup !== false) {
                $cueCount = count($sup->getCues());

                $supFormat = class_basename($sup);
            }
        }
        catch(Exception $exception) {
            $sup = false;
        }

        SupJobMeta::create([
            'sup_job_id' => $this->supJob->id,
            'format'     => ($sup === false) ? 'Failed to open' : $supFormat,
            'cue_count'  => ($sup === false) ? null : $cueCount,
        ]);
    }
}
