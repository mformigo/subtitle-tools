<?php

namespace App\Http\Controllers\Admin;

use App\Models\FileJob;

class FileJobsController
{
    public function index()
    {
        $fileJobs = FileJob::query()
            ->with('inputStoredFile')
            ->with('inputStoredFile.meta')
            ->orderByDesc('id')
            ->take(1200)
            ->get();

        return view('admin.filejobs', ['fileJobs' => $fileJobs]);
    }
}
