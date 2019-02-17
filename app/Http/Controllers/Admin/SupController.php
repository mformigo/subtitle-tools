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
            ->take(200)
            ->get();

        return view('admin.sup', [
            'sups' => $sups,
            'supCacheHitList' => SupJob::orderByDesc('cache_hits')->take(5)->get(),
        ]);
    }
}
