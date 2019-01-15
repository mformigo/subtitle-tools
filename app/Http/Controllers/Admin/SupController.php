<?php

namespace App\Http\Controllers\Admin;

use App\Models\SupJob;

class SupController
{
    public function index()
    {
        $sups = SupJob::query()
            ->with('meta')
            ->with('inputStoredFile')
            ->with('inputStoredFile.meta')
            ->orderBy('created_at', 'DESC')
            ->take(1000)
            ->get();

        return view('admin.sup')->with('sups', $sups);
    }

    public function retry(SupJob $supJob)
    {
        $supJob->retry();

        return back();
    }
}
