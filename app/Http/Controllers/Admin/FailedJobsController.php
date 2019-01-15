<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;

class FailedJobsController
{
    public function index()
    {
        $failedJobs = DB::table('failed_jobs')->get();

        return view('admin.failed-jobs')->with('failedJobs', $failedJobs);
    }

    public function truncate()
    {
        DB::table('failed_jobs')->truncate();

        return back();
    }
}
