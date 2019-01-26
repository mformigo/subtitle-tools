<?php

namespace Tests;

use App\Models\StoredFile;
use App\Models\SupJob;

trait CreatesModels
{
    public function createStoredFile($attributes = []): StoredFile
    {
        return factory(StoredFile::class)->create($attributes);
    }

    public function createSupJob($attributes = []): SupJob
    {
        return factory(SupJob::class)->create($attributes);
    }
}
