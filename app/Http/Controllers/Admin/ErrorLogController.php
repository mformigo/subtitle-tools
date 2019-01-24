<?php

namespace App\Http\Controllers\Admin;

class ErrorLogController
{
    public function delete()
    {
        $filePath = storage_path('logs/laravel.log');

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return back();
    }
}
