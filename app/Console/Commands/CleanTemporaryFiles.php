<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanTemporaryFiles extends Command
{
    protected $signature = 'st:clean-temporary-files';

    protected $description = 'Delete all temporary files older than one day';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $temporaryFilesDir = storage_disk_file_path('temporary-files/');

        $todaysPrefix = date('Y-z') . '-';

        $fileNames = scandir($temporaryFilesDir);

        $fileNames = array_filter($fileNames, function ($name) use ($todaysPrefix) {
            return !starts_with($name, ['.', $todaysPrefix]);
        });

        foreach ($fileNames as $name) {
            unlink($temporaryFilesDir . $name);
        }
    }
}
