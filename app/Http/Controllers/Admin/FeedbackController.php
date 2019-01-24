<?php

namespace App\Http\Controllers\Admin;

class FeedbackController
{
    public function delete()
    {
        $filePath = storage_path('logs/feedback.log');

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return back();
    }
}
