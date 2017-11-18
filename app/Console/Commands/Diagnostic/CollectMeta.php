<?php

namespace App\Console\Commands\Diagnostic;

use App\Jobs\Diagnostic\CollectStoredFileMetaJob;
use App\Jobs\Diagnostic\CollectSubIdxMetaJob;
use App\Jobs\Diagnostic\CollectSupMetaJob;
use App\Models\StoredFile;
use App\Models\SubIdx;
use App\Models\SupJob;
use Illuminate\Console\Command;

class CollectMeta extends Command
{
    protected $signature = 'st:collect-meta
                            {--m|many : queue more jobs than normal}
                            {--a|all : queue all the jobs}';

    protected $description = 'Create jobs for collecting meta';

    public function handle()
    {
        $multiplier = $this->option('all') ? 9999 : ($this->option('many') ? 5 : 1);

        $this->collectStoredFileMeta($multiplier);

        $this->collectSubIdxMeta($multiplier);

        $this->collectSupMeta($multiplier);
    }

    protected function collectStoredFileMeta(int $multiplier)
    {
        StoredFile::query()
            ->doesntHave('meta')
            ->take(1000 * $multiplier)
            ->get()
            ->each(function ($storedFile) {
                CollectStoredFileMetaJob::dispatch($storedFile)->onQueue('low-fast');
            });
    }

    protected function collectSubIdxMeta(int $multiplier)
    {
        $subIdxesWithoutMeta = SubIdx::query()
            ->doesntHave('meta')
            ->take(50 * $multiplier)
            ->get();

        foreach($subIdxesWithoutMeta as $subIdx) {
            $allFinished = true;

            foreach($subIdx->languages as $language) {
                if(! $language->hasFinished) {
                    $allFinished = false;
                    break;
                }
            }

            if($allFinished) {
                CollectSubIdxMetaJob::dispatch($subIdx)->onQueue('low-fast');
            }
        }
    }

    protected function collectSupMeta(int $multiplier)
    {
        SupJob::query()
            ->doesntHave('meta')
            ->take(50 * $multiplier)
            ->get()
            ->each(function ($supJob) {
                CollectSupMetaJob::dispatch($supJob)->onQueue('low-fast');
            });
    }
}
