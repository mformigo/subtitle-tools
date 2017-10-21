<?php

namespace App\Utils;

use Illuminate\Support\Facades\Storage;

class TempDir
{
    public function make($identifier = 'temp')
    {
        $newDirectoryName = date('Y-z') . '-' . $identifier . '-' . str_random(16);

        Storage::makeDirectory('temporary-dirs/'.$newDirectoryName);

        return storage_disk_file_path('temporary-dirs/') . $newDirectoryName;
    }
}
