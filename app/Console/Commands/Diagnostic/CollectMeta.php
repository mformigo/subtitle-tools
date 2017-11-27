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
            ->tap(function ($collection) {
                $this->info('Dispatching '.count($collection).' stored file meta jobs');
            })
            ->each(function ($storedFile) {
                CollectStoredFileMetaJob::dispatch($storedFile)->onQueue('low-fast');
            });
    }

    protected function collectSubIdxMeta(int $multiplier)
    {
        SubIdx::query()
            ->doesntHave('meta')
            ->with('languages')
            ->take(50 * $multiplier)
            ->get()
            ->filter(function ($subIdx) {
                return $subIdx->languages->every(function ($language) {
                    return $language->hasFinished;
                });
            })
            ->tap(function ($collection) {
                $this->info('Dispatching '.count($collection).' sub/idx meta jobs');
            })
            ->each(function ($subIdx) {
                CollectSubIdxMetaJob::dispatch($subIdx)->onQueue('low-fast');
            });
    }

    protected function collectSupMeta(int $multiplier)
    {
        SupJob::query()
            ->whereNotNull('finished_at')
            ->doesntHave('meta')
            ->take(50 * $multiplier)
            ->get()
            ->tap(function ($collection) {
                $this->info('Dispatching '.count($collection).' sup meta jobs');
            })
            ->each(function ($supJob) {
                CollectSupMetaJob::dispatch($supJob)->onQueue('low-fast');
            });
    }
}
