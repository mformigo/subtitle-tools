<?php

namespace App\Console\Commands;

use App\Jobs\Diagnostic\CollectStoredFileMetaJob;
use App\Models\StoredFile;
use Illuminate\Console\Command;

class QueueCollectStoredFileMetaJobs extends Command
{
    protected $signature = 'st:collect-stored-file-meta';

    protected $description = 'Create jobs for all stored files that don\'t have meta yet';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $storedFilesWithoutMeta = StoredFile::query()
            ->doesntHave('meta')
            ->take(500)
            ->get();

        foreach($storedFilesWithoutMeta as $storedFile) {
            dispatch(
                (new CollectStoredFileMetaJob($storedFile))->onQueue('low-fast')
            );
        }
    }
}
