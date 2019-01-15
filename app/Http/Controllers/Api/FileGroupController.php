<?php

namespace App\Http\Controllers\Api;

use App\Jobs\ZipFileGroupJob;
use App\Models\FileGroup;

class FileGroupController
{
    public function result($urlKey)
    {
        return FileGroup::query()
            ->where('url_key', $urlKey)
            ->firstOrFail()
            ->fileJobs
            ->map(function ($fileJob) {
                return $fileJob->getApiValues();
            });
    }

    public function archive($urlKey)
    {
        return FileGroup::query()
            ->where('url_key', $urlKey)
            ->firstOrFail()
            ->getApiValues();
    }

    public function requestArchive($urlKey)
    {
        $fileGroup = FileGroup::query()
            ->where('url_key', $urlKey)
            ->whereNotNull('file_jobs_finished_at')
            ->whereNull('archive_requested_at')
            ->firstOrFail();

        ZipFileGroupJob::dispatch($fileGroup);

        return '1';
    }
}
