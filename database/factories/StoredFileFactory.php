<?php

use App\Models\StoredFile;
use Faker\Generator as Faker;

$factory->define(StoredFile::class, function (Faker $faker) {
    $hash = sha1(str_random(40));

    $storagePath = 'stored-files/'.now()->format('Y-W/z/U').'-'.substr($hash, 0 ,16);

    if (! Storage::exists($storagePath)) {
        Storage::put($storagePath, 'abc123');
    }

    return [
        'hash' => $hash,
        'storage_file_path' => $storagePath,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
