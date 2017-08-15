<?php

namespace App\Http\Controllers\Api;

use App\Models\FileGroup;
use App\Http\Controllers\Controller;

class FileGroupController extends Controller
{
    public function result($urlKey)
    {
        return FileGroup::where('url_key', $urlKey)
            ->firstOrFail()
            ->fileJobs()
            ->get()
            ->map(function($fileJob) {
                return $fileJob->getApiValues();
            });
    }
}
