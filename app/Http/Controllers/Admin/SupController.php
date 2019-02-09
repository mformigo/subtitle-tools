<?php

namespace App\Http\Controllers\Admin;

use App\Models\SupJob;

class SupController
{
    public function index()
    {
        $sups = SupJob::query()
            ->with('meta', 'inputStoredFile', 'inputStoredFile.meta')
            ->orderByDesc('created_at')
            ->take(1000)
            ->get();

        return view('admin.sup', [
            'sups' => $sups,
            'supCacheHitList' => SupJob::orderByDesc('cache_hits')->take(5)->get(),
        ]);
    }

    public function retry(SupJob $supJob)
    {
        dd('re-trying sup-jobs has been removed');
    }
}
