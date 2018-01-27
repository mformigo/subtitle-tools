<?php

namespace App\Http\Controllers\FileJobs;

use App\Jobs\FileJobs\ConvertToVttJob;
use Illuminate\Http\Request;

class ConvertToVttController extends FileJobController
{
    public function index()
    {
        return view('tools.convert-to-vtt');
    }

    public function post(Request $request)
    {
        $this->validateFileJob();

        return $this->doFileJobs(ConvertToVttJob::class);
    }

    protected function getIndexRouteName()
    {
        return 'convertToVtt';
    }
}
