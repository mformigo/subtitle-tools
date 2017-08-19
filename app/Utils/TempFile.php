<?php

namespace App\Utils;

use Illuminate\Support\Facades\Storage;

class TempFile
{
    public function make($content)
    {
        $tempFilePath = $this->makeFilePath();

        file_put_contents($tempFilePath, $content);

        register_shutdown_function(function() use ($tempFilePath) {
            if(file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        });

        return $tempFilePath;
    }

    public function makeFilePath($identifier = 'temp')
    {
        $directory = storage_disk_file_path('temporary-files/');

        if(!file_exists($directory)) {
            Storage::makeDirectory('temporary-files/');
        }

        return $directory . date('Y-z') . '-' . $identifier . '-' . str_random(16);
    }
}
