<?php

namespace App\Jobs\Diagnostic;

use App\Jobs\BaseJob;
use App\Models\Diagnostic\SupJobMeta;
use App\Models\SupJob;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use SjorsO\Sup\SupFile;

class CollectSupMetaJob extends BaseJob implements ShouldQueue
{
    protected $supJob;

    public function __construct(SupJob $supJob)
    {
        $this->supJob = $supJob;
    }

    public function handle()
    {
        $supFilePath = $this->supJob->inputStoredFile->file_path;

        $supFormat = class_basename(SupFile::getFormat($supFilePath));

        $failedToOpen = false;
        $cueCount = null;

        try {
            $sup = SupFile::open($supFilePath);

            if ($sup !== false) {
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

    public function failed(Exception $exception)
    {
        \Log::error("Failed collecting sup meta (SupJobId: {$this->supJob->id})");
        \Log::error($exception->getMessage());
    }
}
