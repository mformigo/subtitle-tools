<?php

namespace App\Http\Controllers\Admin;

use App\Models\SubIdx;

class SubIdxController extends Controller
{
    public function index()
    {
        $subIdxes = SubIdx::query()
            ->with('meta')
            ->with('languages')
            ->orderBy('id', 'DESC')
            ->take(200)
            ->get();

        return view('admin.sub-idx')->with('subIdxes', $subIdxes);
    }
}
