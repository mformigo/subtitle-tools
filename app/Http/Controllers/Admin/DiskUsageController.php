<?php

namespace App\Http\Controllers\Admin;

use App\Models\DiskUsage;

class DiskUsageController
{
    public function index()
    {
        return view('admin.disk-usage', [
            'diskUsages' => DiskUsage::orderByDesc('created_at')->take(1000)->get(),
        ]);
    }
}
