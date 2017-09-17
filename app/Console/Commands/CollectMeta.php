<?php

namespace App\Console\Commands;

use App\Jobs\Diagnostic\CollectStoredFileMetaJob;
use App\Jobs\Diagnostic\CollectSubIdxMetaJob;
use App\Models\StoredFile;
use App\Models\SubIdx;
use Illuminate\Console\Command;

class CollectMeta extends Command
{
    protected $signature = 'st:collect-meta
                            {--m|many : queue more jobs than normal}
                            {--a|all : queue all the jobs}';

    protected $description = 'Create jobs for collecting meta';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $multiplier = $this->option('all') ? 9999 : ($this->option('many') ? 5 : 1);

        $this->collectStoredFileMeta($multiplier);

        $this->collectSubIdxMeta($multiplier);
    }

    protected function collectStoredFileMeta($multiplier)
    {
        $storedFilesWithoutMeta = StoredFile::query()
            ->doesntHave('meta')
            ->take(1000 * $multiplier)
            ->get();

        foreach($storedFilesWithoutMeta as $storedFile) {
            dispatch(
                (new CollectStoredFileMetaJob($storedFile))->onQueue('low-fast')
            );
        }
    }

    protected function collectSubIdxMeta($multiplier)
    {
        $subIdxesWithoutMeta = SubIdx::query()
            ->doesntHave('meta')
            ->take(50 * $multiplier)
            ->get();

        foreach($subIdxesWithoutMeta as $subIdx) {
            $allFinished = true;

            foreach($subIdx->languages as $language) {
                if(!$language->hasFinished) {
                    $allFinished = false;
                    break;
                }
            }

            if($allFinished) {
                dispatch(
                    (new CollectSubIdxMetaJob($subIdx))->onQueue('low-fast')
                );
            }
        }
    }
}
