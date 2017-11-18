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
        list($openedCorrectly, $sup) = $this->openSup();

        $meta = new SupJobMeta([
            'format'    => class_basename($sup),
            'cue_count' => $openedCorrectly ? count($sup->getCues()) : null,
        ]);

        $this->supJob->meta()->save($meta);
    }

    protected function openSup()
    {
        try {
            $sup = SupFile::open($this->supJob->inputStoredFile->file_path);
        }
        catch(Exception $exception) {
            $sup = false;
        }

        if($sup === false) {
            return [false, 'Failed to open'];
        }

        return [true, $sup];
    }
}
