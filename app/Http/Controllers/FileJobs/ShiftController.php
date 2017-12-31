<?php

namespace App\Http\Controllers\FileJobs;

use App\Jobs\FileJobs\ShiftJob;
use Illuminate\Http\Request;

class ShiftController extends FileJobController
{
    public function index()
    {
        return view('tools.shifter');
    }

    public function post(Request $request)
    {
        $this->validateFileJob([
            'milliseconds' => 'required|numeric|not_in:0|regex:/^(-?\d+)$/',
        ]);

        $jobOptions = [
            'milliseconds' => $request->get('milliseconds'),
        ];

        return $this->doFileJobs(ShiftJob::class, $jobOptions);
    }

    protected function getIndexRouteName()
    {
        return 'shift';
    }
}
