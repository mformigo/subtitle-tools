<?php

namespace App\Http\Controllers\Admin;

class DiskUsageController extends Controller
{
    public function index()
    {
        $output = shell_exec('du -h --total '.storage_path('app'));

        $output = str_replace(storage_path('app/'), '', $output);

        $lines = explode("\n", $output);

        $dirs = collect($lines)
            ->mapToGroups(function ($string) {
                return [str_before(str_after($string, "\t"), '/') => $string];
            });


        return view('admin.disk-usage', [
            'dirs' => $dirs,
        ]);
    }
}
