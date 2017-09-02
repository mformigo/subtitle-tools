<?php

namespace App\Http\Controllers\Admin;

use App\Facades\TextFileReader;

class DashboardController extends Controller
{
    public function index()
    {
        $logsWithErrors = collect(scandir(storage_path('logs')))->filter(function($name) {
            return !starts_with($name, '.') && filesize(storage_path("logs/{$name}")) > 0;
        })->values()->all();

        return view('admin.dashboard', [
            'logs' => $logsWithErrors,
        ]);
    }

    public function getLog($name)
    {
        $filePath = storage_path("logs/{$name}");

        if(!file_exists($filePath)) {
            return back();
        }

        return implode('<br />', TextFileReader::getLines($filePath));
    }

    public function deleteLog($name)
    {
        $filePath = storage_path("logs/{$name}");

        if(file_exists($filePath)) {
            unlink($filePath);
        }

        return back();
    }
}
