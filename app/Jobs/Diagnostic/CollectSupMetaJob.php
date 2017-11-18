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
        $supFilePath = $this->supJob->inputStoredFile->file_path;

        $failedToOpen = false;

        $supFormat = class_basename(SupFile::getFormat($supFilePath));

        $cueCount = null;

        try {
            $sup = SupFile::open($supFilePath);

            if($sup !== false) {
                $cueCount = count($sup->getCues());
            }
        }
        catch(Exception $exception) {
            $failedToOpen = true;
        }

        SupJobMeta::create([
            'sup_job_id'     => $this->supJob->id,
            'format'         => $supFormat,
            'cue_count'      => $cueCount,
            'failed_to_open' => $failedToOpen,
        ]);
    }
}
