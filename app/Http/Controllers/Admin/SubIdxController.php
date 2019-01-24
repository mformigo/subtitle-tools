<?php

namespace App\Http\Controllers\Admin;

use App\Models\SubIdx;

class SubIdxController
{
    public function index()
    {
        $subIdxes = SubIdx::query()
            ->with('meta', 'languages')
            ->orderByDesc('id')
            ->take(200)
            ->get();

        return view('admin.sub-idx', [
            'subIdxes' => $subIdxes,
            'subIdxCacheHitList' => SubIdx::orderByDesc('cache_hits')->take(5)->get(),
        ]);
    }
}
