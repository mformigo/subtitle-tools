<?php

namespace App\Http\Controllers\Admin;

use App\Models\SubIdx;
use App\Models\SubIdxLanguage;

class SubIdxController
{
    public function index()
    {
        $subIdxes = SubIdx::query()
            ->with('languages')
            ->orderByDesc('id')
            ->take(200)
            ->get();

        return view('admin.sub-idx', [
            'subIdxes' => $subIdxes,
            'subIdxCacheHitList' => SubIdx::orderByDesc('cache_hits')->take(5)->get(),
            'filesInQueue' => SubIdxLanguage::whereNotNull('queued_at')->whereNull('started_at')->count(),
        ]);
    }
}
