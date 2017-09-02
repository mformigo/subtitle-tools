<?php

namespace App\Http\Controllers;

use App\Jobs\ConvertToUtf8Job;
use Illuminate\Http\Request;

class ConvertToUtf8Controller extends FileJobController
{
    public function index()
    {
        return view('guest.convert-to-utf8');
    }

    public function post(Request $request)
    {
        $this->validateFileJob();

        return $this->doFileJobs(ConvertToUtf8Job::class);
    }

    protected function getIndexRouteName()
    {
        return 'convertToUtf8';
    }
}
