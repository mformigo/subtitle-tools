<?php

namespace App\Utils;

class TempFile
{
    public function make($content)
    {
        $tempFilePath = storage_disk_file_path('temporary-files/temp-' . str_random(16));

        file_put_contents($tempFilePath, $content);

        register_shutdown_function(function() use ($tempFilePath) {
            if(file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        });

        return $tempFilePath;
    }
}
