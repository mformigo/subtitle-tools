<?php

namespace App\Http\Controllers\Admin;

class LogsController
{
    public function show($name)
    {
        $filePath = storage_path("logs/{$name}");

        if (! file_exists($filePath)) {
            return back();
        }

        if ($name === 'laravel.log') {
            return view('admin.log', [
                'name' => $name,
                'lines' => read_lines($filePath),
            ]);
        }

        return implode('<br />', read_lines($filePath));
    }

    public function delete($name)
    {
        $filePath = storage_path("logs/{$name}");

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return back();
    }
}
