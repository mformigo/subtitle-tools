<?php

namespace App\Http\Controllers\Admin;

use App\Models\FileJob;

class FileJobsController extends Controller
{
    public function index()
    {
        $fileJobs = FileJob::query()
            ->with('inputStoredFile')
            ->with('inputStoredFile.meta')
            ->orderBy('id', 'DESC')
            ->take(800)
            ->get();

        return view('admin.filejobs')->with('fileJobs', $fileJobs);
    }
}
