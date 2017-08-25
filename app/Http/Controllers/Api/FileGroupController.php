<?php

namespace App\Http\Controllers\Api;

use App\Jobs\ZipFileGroupJob;
use App\Models\FileGroup;
use App\Http\Controllers\Controller;

class FileGroupController extends Controller
{
    public function result($urlKey)
    {
        return FileGroup::where('url_key', $urlKey)
            ->firstOrFail()
            ->fileJobs
            ->map(function($fileJob) {
                return $fileJob->getApiValues();
            });
    }

    public function archive($urlKey)
    {
        return FileGroup::where('url_key', $urlKey)
            ->firstOrFail()
            ->getApiValues();
    }

    public function requestArchive($urlKey)
    {
        $fileGroup = FileGroup::where('url_key', $urlKey)
            ->whereNotNull('file_jobs_finished_at')
            ->whereNull('archive_requested_at')
            ->firstOrFail();

        $this->dispatch(new ZipFileGroupJob($fileGroup));

        return '1';
    }
}
