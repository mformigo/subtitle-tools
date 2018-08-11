<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic\FileJobStats;

class StatsController extends Controller
{
    public function index()
    {
        return view('stats', [
            'fileJobStatsYesterday' => FileJobStats::yesterday(),
            'fileJobStatsLastMonth' => FileJobStats::lastMonth(),
            'fileJobStatsAllTime'   => FileJobStats::allTime(),
        ]);
    }
}
