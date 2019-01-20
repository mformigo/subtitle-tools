<?php

use App\Models\StoredFile;
use Illuminate\Database\Seeder;

class StoredFilesTableSeeder extends Seeder
{
    public function run()
    {
        StoredFile::each(function (StoredFile $storedFile) {
            if (! Storage::exists($storedFile->storage_file_path)) {
                Storage::put($storedFile->storage_file_path, 'abc123');
            }
        });
    }
}
