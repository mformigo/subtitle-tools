<?php

namespace App\Http\Controllers\FileJobs;

use App\Jobs\FileJobs\ConvertToPlainTextJob;

class ConvertToPlainTextController extends FileJobController
{
    protected $indexRouteName = 'convertToPlainText';

    protected $job = ConvertToPlainTextJob::class;

    public function index()
    {
        return view('tools.convert-to-plain-text');
    }
}
