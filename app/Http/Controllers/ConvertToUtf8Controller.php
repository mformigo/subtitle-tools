<?php

namespace App\Http\Controllers;

use App\Jobs\FileJobs\ConvertToUtf8Job;

class ConvertToUtf8Controller extends FileJobController
{
    protected $indexRouteName = 'convertToUtf8';

    protected $job = ConvertToUtf8Job::class;

    public function index()
    {
        return view('tools.convert-to-utf8');
    }
}
