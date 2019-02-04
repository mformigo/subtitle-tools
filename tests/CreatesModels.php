<?php

namespace Tests;

use App\Models\FileGroup;
use App\Models\FileJob;
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

    public function createFileGroup($attributes = []): FileGroup
    {
        /** @var FileGroup $fileGroup */
        $fileGroup = factory(FileGroup::class)->create($attributes);

        $fileGroup->fileJobs()->save(
            $this->makeFileJob()
        );

        return $fileGroup;
    }

    public function makeFileJob($attributes = []): FileJob
    {
        return factory(FileJob::class)->make($attributes);
    }
}
