<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;

class FailedJobsController
{
    public function truncate()
    {
        DB::table('failed_jobs')->truncate();

        return back();
    }
}
