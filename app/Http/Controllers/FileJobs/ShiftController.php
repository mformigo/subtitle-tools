<?php

namespace App\Http\Controllers\FileJobs;

use App\Jobs\FileJobs\ShiftJob;
use Illuminate\Http\Request;

class ShiftController extends FileJobController
{
    protected $indexRouteName = 'shift';

    protected $job = ShiftJob::class;

    public function index()
    {
        return view('tools.shifter');
    }

    protected function rules(): array
    {
        return [
            'milliseconds' => 'required|numeric|not_in:0|regex:/^(-?\d+)$/',
        ];
    }

    protected function options(Request $request)
    {
        return [
            'milliseconds' => $request->get('milliseconds'),
        ];
    }
}
