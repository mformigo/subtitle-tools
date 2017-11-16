<?php

namespace App\Http\Controllers\FileJobs;

use App\Jobs\FileJobs\ConvertToPlainTextJob;
use Illuminate\Http\Request;

class ConvertToPlainTextController extends FileJobController
{
    public function index()
    {
        return view('guest.convert-to-plain-text');
    }

    public function post(Request $request)
    {
        $this->validateFileJob();

        return $this->doFileJobs(ConvertToPlainTextJob::class);
    }

    protected function getIndexRouteName()
    {
        return 'convertToPlainText';
    }
}
