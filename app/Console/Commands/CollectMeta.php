<?php

namespace App\Console\Commands;

use App\Jobs\Diagnostic\CollectStoredFileMetaJob;
use App\Jobs\Diagnostic\CollectSubIdxMetaJob;
use App\Models\StoredFile;
use App\Models\SubIdx;
use Illuminate\Console\Command;

class CollectMeta extends Command
{
    protected $signature = 'st:collect-meta';

    protected $description = 'Create jobs for collecting meta';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->collectStoredFileMeta();

        $this->collectSubIdxMeta();
    }

    protected function collectStoredFileMeta()
    {
        $storedFilesWithoutMeta = StoredFile::query()
            ->doesntHave('meta')
            ->take(1000)
            ->get();

        foreach($storedFilesWithoutMeta as $storedFile) {
            dispatch(
                (new CollectStoredFileMetaJob($storedFile))->onQueue('low-fast')
            );
        }
    }

    protected function collectSubIdxMeta()
    {
        $subIdxesWithoutMeta = SubIdx::query()
            ->doesntHave('meta')
            ->take(50)
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
