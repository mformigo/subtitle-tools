<?php

namespace App\Http\Controllers\Admin;

use App\Models\FileJob;

class FileJobsController extends Controller
{
    public function index()
    {
        $fileJobs = FileJob::query()
            ->orderBy('id', 'DESC')
            ->take(200)
            ->get();

        return view('admin.filejobs')->with('fileJobs', $fileJobs);
    }
}
