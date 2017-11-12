<?php

namespace App\Http\Controllers;

use App\Jobs\FileJobs\ShiftJob;
use Illuminate\Http\Request;

class ShiftController extends FileJobController
{
    public function index()
    {
        return view('guest.shift');
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
