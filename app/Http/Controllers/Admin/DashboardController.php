<?php

namespace App\Http\Controllers\Admin;

use App\Facades\TextFileReader;

class DashboardController extends Controller
{
    protected $supervisorWorkers = [
        'st-worker-default',
        'st-worker-broadcast',
        'st-worker-subidx',
    ];

    public function index()
    {
        $logsWithErrors = collect(scandir(storage_path('logs')))->filter(function($name) {
            return !starts_with($name, '.') && filesize(storage_path("logs/{$name}")) > 0;
        })->values()->all();

        $supervisorInfo = $this->getSupervisorInfo();

        return view('admin.dashboard', [
            'logs'       => $logsWithErrors,
            'supervisor' => $supervisorInfo,
            'goodSupervisor' => count($this->supervisorWorkers) === count($supervisorInfo),
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

    private function getSupervisorInfo()
    {
        if(app()->environment('local')) {
            $lines = [
                "st-worker-broadcast:st-worker-broadcast_00   RUNNING   pid 27243, uptime 0:04:36",
                "st-worker-default:st-worker-default_00       RUNNING   pid 27245, uptime 0:13:51",
                "st-worker-subidx:st-worker-subidx_00         RUNNING   pid 27244, uptime 2:23:40",
            ];
        }
        else {
            $lines = explode("\n", shell_exec('supervisorctl status'));
        }

        return collect($lines)->filter(function($line) {
            return !empty($line);
        })->map(function($line) {
           return preg_split('/ {3,}|, /', $line);
        })->map(function($parts) {
            return (object)[
                'worker'    => str_before($parts[0], ':'),
                'name'      => str_after($parts[0], ':'),
                'status'    => strtolower($parts[1] ?? 'UNKNOWN'),
                'isRunning' => $parts[1] ?? 'UNKNOWN' === 'RUNNING',
                'pid'       => str_after($parts[2] ?? '?', 'pid '),
                'uptime'    => str_after($parts[3] ?? '?:??:??', 'uptime '),
            ];
        })->all();
    }
}
